import { zodResolver } from "@hookform/resolvers/zod";
import { useEffect, useMemo, useRef, useState } from "react";
import { useForm } from "react-hook-form";
import { DataTable, type DataColumn } from "../../components/common/DataTable";
import { getErrorMessage } from "../../services/api";
import { schoolService, type SchoolItem } from "../../services/schoolService";
import {
  userService,
  type PermissionModule,
  type RoleSummary,
  type RoleUserPermissionTarget,
  type UserFormPayload,
  type UserOption
} from "../../services/userService";
import { useAuthStore } from "../../stores/authStore";
import { getPermissionModuleLabel, getRoleLabel } from "../../lib/uiText";
import { userFormSchema } from "../../lib/formSchemas";
import { cn } from "../../lib/utils";

type UserFormValues = {
  full_name: string;
  email: string;
  phone: string;
  role: string;
  school_id: string;
  school_ids: string[];
  password: string;
  must_change_password: boolean;
};

type UsersSection = "list" | "form" | "permissions";

const defaultUserForm: UserFormValues = {
  full_name: "",
  email: "",
  phone: "",
  role: "teacher",
  school_id: "",
  school_ids: [],
  password: "",
  must_change_password: true
};

function toUserPayload(values: UserFormValues, editingUser: UserOption | null): UserFormPayload {
  const normalizedSchoolIds = values.school_ids
    .map((schoolId) => Number(schoolId))
    .filter((schoolId) => Number.isInteger(schoolId) && schoolId > 0);
  const primarySchoolId =
    values.role === "supervisor"
      ? (normalizedSchoolIds[0] ?? (values.school_id ? Number(values.school_id) : null))
      : (values.school_id ? Number(values.school_id) : null);

  return {
    full_name: values.full_name.trim(),
    email: values.email.trim() || null,
    phone: values.phone.trim() || null,
    role: values.role,
    school_id: primarySchoolId,
    ...(values.role === "supervisor" ? { school_ids: normalizedSchoolIds } : {}),
    ...(values.password.trim() ? { password: values.password.trim() } : {}),
    must_change_password: editingUser ? values.must_change_password : true
  };
}

function toUserForm(user: UserOption): UserFormValues {
  return {
    full_name: user.full_name ?? "",
    email: user.email ?? "",
    phone: user.phone ?? "",
    role: user.role ?? "teacher",
    school_id: user.school_id ? String(user.school_id) : "",
    school_ids: (user.assigned_schools ?? []).map((school) => String(school.id)),
    password: "",
    must_change_password: false
  };
}

export function UsersPage() {
  const user = useAuthStore((state) => state.user);
  const isSuperAdmin = user?.role === "super_admin";
  const [rows, setRows] = useState<UserOption[]>([]);
  const [schools, setSchools] = useState<SchoolItem[]>([]);
  const [roles, setRoles] = useState<RoleSummary[]>([]);
  const [permissionModules, setPermissionModules] = useState<PermissionModule[]>([]);
  const [roleUsers, setRoleUsers] = useState<RoleUserPermissionTarget[]>([]);
  const [editingUser, setEditingUser] = useState<UserOption | null>(null);
  const [selectedRoleName, setSelectedRoleName] = useState("teacher");
  const [selectedRoleUserIds, setSelectedRoleUserIds] = useState<string[]>([]);
  const [selectedPermissionKeys, setSelectedPermissionKeys] = useState<string[]>([]);
  const [searchInput, setSearchInput] = useState("");
  const [roleFilter, setRoleFilter] = useState("");
  const [statusFilter, setStatusFilter] = useState("");
  const [supervisorSchoolSearch, setSupervisorSchoolSearch] = useState("");
  const [roleUsersSearch, setRoleUsersSearch] = useState("");
  const [loading, setLoading] = useState(true);
  const [savingUser, setSavingUser] = useState(false);
  const [savingPermissions, setSavingPermissions] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(null);
  const [isSupervisorSchoolsDropdownOpen, setIsSupervisorSchoolsDropdownOpen] = useState(false);
  const [isRoleUsersDropdownOpen, setIsRoleUsersDropdownOpen] = useState(false);
  const [activeSection, setActiveSection] = useState<UsersSection>("list");
  const [selectedUserDetails, setSelectedUserDetails] = useState<UserOption | null>(null);
  const formSectionRef = useRef<HTMLElement | null>(null);
  const permissionsSectionRef = useRef<HTMLElement | null>(null);
  const {
    formState: { errors: userFormErrors },
    handleSubmit: handleUserFormSubmit,
    register: registerUserForm,
    reset: resetUserForm,
    setValue: setUserFormValue,
    watch: watchUserForm
  } = useForm<UserFormValues>({
    defaultValues: defaultUserForm,
    resolver: zodResolver(userFormSchema)
  });
  const userForm = watchUserForm();
  const isSupervisorRole = userForm.role === "supervisor";
  const supervisorSchoolsDropdownRef = useRef<HTMLDivElement | null>(null);
  const roleUsersDropdownRef = useRef<HTMLDivElement | null>(null);

  const selectedRole = useMemo(
    () => roles.find((item) => item.name === selectedRoleName) ?? null,
    [roles, selectedRoleName]
  );
  const filteredSupervisorSchools = useMemo(() => {
    const normalizedSearch = supervisorSchoolSearch.trim();

    if (!normalizedSearch) {
      return schools;
    }

    return schools.filter((school) => school.name.includes(normalizedSearch));
  }, [schools, supervisorSchoolSearch]);
  const filteredRoleUsers = useMemo(() => {
    const normalizedSearch = roleUsersSearch.trim();

    if (!normalizedSearch) {
      return roleUsers;
    }

    return roleUsers.filter((roleUser) =>
      [roleUser.full_name, roleUser.email, roleUser.school?.name_ar, ...(roleUser.assigned_schools?.map((school) => school.name_ar) ?? [])]
        .filter(Boolean)
        .some((value) => String(value).includes(normalizedSearch))
    );
  }, [roleUsers, roleUsersSearch]);
  const isAllRoleUsersSelected = selectedRoleUserIds.includes("__all__");

  const jumpToSection = (section: UsersSection) => {
    setActiveSection(section);
    window.setTimeout(() => {
      if (section === "form") {
        formSectionRef.current?.scrollIntoView({ behavior: "smooth", block: "start" });
      }

      if (section === "permissions") {
        permissionsSectionRef.current?.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    }, 0);
  };

  const selectUserForPermissions = (targetUser: UserOption) => {
    if (targetUser.role) {
      setSelectedRoleName(targetUser.role);
    }

    setSelectedRoleUserIds([String(targetUser.id)]);
    jumpToSection("permissions");
  };

  useEffect(() => {
    if (!isSuperAdmin) {
      return;
    }

    const load = async () => {
      setLoading(true);

      try {
        const [usersPayload, schoolsPayload, rolesPayload, permissionsPayload] = await Promise.all([
          userService.list({
            per_page: 100,
            "filter[search]": searchInput.trim() || undefined,
            "filter[role]": roleFilter || undefined,
            "filter[status]": statusFilter || undefined
          }),
          schoolService.list({ perPage: 100 }),
          userService.listRoles(),
          userService.listPermissions()
        ]);

        setRows(usersPayload.data);
        setSchools(schoolsPayload.data);
        setRoles(rolesPayload);
        setPermissionModules(permissionsPayload);

        if (!selectedRoleName && rolesPayload.length) {
          setSelectedRoleName(rolesPayload[0].name);
        }
      } catch (loadError) {
        setError(getErrorMessage(loadError));
      } finally {
        setLoading(false);
      }
    };

    void load();
  }, [isSuperAdmin, roleFilter, searchInput, selectedRoleName, statusFilter]);

  useEffect(() => {
    if (selectedRole) {
      setSelectedPermissionKeys(selectedRole.permissions.map((permission) => permission.key));
    }
  }, [selectedRole]);

  useEffect(() => {
    setSelectedRoleUserIds([]);
    setRoleUsersSearch("");
    setIsRoleUsersDropdownOpen(false);
  }, [selectedRoleName]);

  useEffect(() => {
    if (!isSupervisorRole) {
      setIsSupervisorSchoolsDropdownOpen(false);
    }
  }, [isSupervisorRole]);

  useEffect(() => {
    if (!selectedRole) {
      setRoleUsers([]);
      setSelectedRoleUserIds([]);
      return;
    }

    void userService
      .listRoleUsers(selectedRole.id)
      .then((payload) => {
        setRoleUsers(payload);
      })
      .catch((loadError) => setError(getErrorMessage(loadError)));
  }, [selectedRole]);

  useEffect(() => {
    if (!selectedRole) {
      return;
    }

    if (selectedRoleUserIds.length === 1 && !isAllRoleUsersSelected) {
      const matchedUser = roleUsers.find((roleUser) => String(roleUser.id) === selectedRoleUserIds[0]);

      if (matchedUser) {
        setSelectedPermissionKeys(matchedUser.effective_permission_keys);
        return;
      }
    }

    setSelectedPermissionKeys(selectedRole.permissions.map((permission) => permission.key));
  }, [isAllRoleUsersSelected, roleUsers, selectedRole, selectedRoleUserIds]);

  useEffect(() => {
    const handleDocumentPointerDown = (event: MouseEvent) => {
      const target = event.target;

      if (!(target instanceof Node)) {
        return;
      }

      if (
        isSupervisorSchoolsDropdownOpen &&
        supervisorSchoolsDropdownRef.current &&
        !supervisorSchoolsDropdownRef.current.contains(target)
      ) {
        setIsSupervisorSchoolsDropdownOpen(false);
      }

      if (
        isRoleUsersDropdownOpen &&
        roleUsersDropdownRef.current &&
        !roleUsersDropdownRef.current.contains(target)
      ) {
        setIsRoleUsersDropdownOpen(false);
      }
    };

    const handleEscapeKey = (event: KeyboardEvent) => {
      if (event.key === "Escape") {
        setIsSupervisorSchoolsDropdownOpen(false);
        setIsRoleUsersDropdownOpen(false);
      }
    };

    document.addEventListener("mousedown", handleDocumentPointerDown);
    document.addEventListener("keydown", handleEscapeKey);

    return () => {
      document.removeEventListener("mousedown", handleDocumentPointerDown);
      document.removeEventListener("keydown", handleEscapeKey);
    };
  }, [isRoleUsersDropdownOpen, isSupervisorSchoolsDropdownOpen]);

  if (!isSuperAdmin) {
    return (
      <section className="page-stack">
        <div className="error-box">هذه الصفحة متاحة لحساب السوبر أدمن فقط.</div>
      </section>
    );
  }

  const columns: DataColumn<UserOption>[] = [
    { key: "name", label: "الاسم", render: (row) => row.full_name },
    { key: "email", label: "البريد", render: (row) => row.email ?? "-" },
    { key: "role", label: "الدور", render: (row) => row.role_display_name_ar ?? getRoleLabel(row.role) },
    {
      key: "school",
      label: "المدرسة",
      render: (row) =>
        row.role === "supervisor" && row.assigned_schools?.length
          ? row.assigned_schools.map((school) => school.name_ar).join("، ")
          : row.school?.name_ar ?? "-"
    },
    {
      key: "status",
      label: "الحالة",
      render: (row) => (
        <span className={cn("status-pill", row.status === "inactive" && "status-pill-inactive")}>
          {row.status === "active" ? "نشط" : "غير نشط"}
        </span>
      )
    },
    {
      key: "actions",
      label: "الإجراءات",
      render: (row) => (
        <div className="button-row compact-actions">
          <button
            className="button button-secondary"
            onClick={() => setSelectedUserDetails(row)}
            type="button"
          >
            عرض
          </button>
          <button
            className="button button-secondary"
            onClick={() => {
              setEditingUser(row);
              resetUserForm(toUserForm(row));
              setSuccessMessage(null);
              setError(null);
              jumpToSection("form");
            }}
            type="button"
          >
            تعديل
          </button>
          <button
            className="button button-secondary"
            onClick={() => selectUserForPermissions(row)}
            type="button"
          >
            الصلاحيات
          </button>
          <button
            className={`button ${row.status === "active" ? "button-ghost" : "button-primary"}`}
            onClick={async () => {
              setSuccessMessage(null);
              setError(null);

              try {
                if (row.status === "active") {
                  await userService.deactivate(row.id);
                  setSuccessMessage(`تم تعطيل الحساب: ${row.full_name}`);
                } else {
                  await userService.changeStatus(row.id, "active");
                  setSuccessMessage(`تم تفعيل الحساب: ${row.full_name}`);
                }

                const refreshed = await userService.list({
                  per_page: 100,
                  "filter[search]": searchInput.trim() || undefined,
                  "filter[role]": roleFilter || undefined,
                  "filter[status]": statusFilter || undefined
                });

                setRows(refreshed.data);
              } catch (actionError) {
                setError(getErrorMessage(actionError));
              }
            }}
            type="button"
          >
            {row.status === "active" ? "تعطيل" : "تفعيل"}
          </button>
        </div>
      )
    }
  ];

  return (
    <section className="page-stack">
      <div className="page-header">
        <div>
          <span className="eyebrow">المشرف العام</span>
          <h2>إدارة الحسابات والصلاحيات</h2>
          <p className="section-description">
            تحكم كامل في الحسابات، الأدوار، وربط المستخدمين بالمدارس عند الحاجة.
          </p>
        </div>
      </div>

      {error ? <div className="error-box">{error}</div> : null}
      {successMessage ? <div className="info-box">{successMessage}</div> : null}

      <div className="section-tabs">
        <button
          className={cn("button", activeSection === "list" ? "button-primary" : "button-secondary")}
          onClick={() => jumpToSection("list")}
          type="button"
        >
          الحسابات
        </button>
        <button
          className={cn("button", activeSection === "form" ? "button-primary" : "button-secondary")}
          onClick={() => {
            if (!editingUser) {
              resetUserForm(defaultUserForm);
            }
            jumpToSection("form");
          }}
          type="button"
        >
          إنشاء أو تعديل حساب
        </button>
        <button
          className={cn("button", activeSection === "permissions" ? "button-primary" : "button-secondary")}
          onClick={() => jumpToSection("permissions")}
          type="button"
        >
          إدارة الصلاحيات
        </button>
      </div>

      {activeSection === "list" ? (
      <section className="surface-card page-stack">
        <div className="page-header">
          <div>
            <span className="eyebrow">المستخدمون</span>
            <h3>الحسابات</h3>
          </div>
        </div>

        <div className="filters-bar filters-bar-wide">
          <label className="field">
            <span>بحث</span>
            <input
              onChange={(event) => setSearchInput(event.target.value)}
              placeholder="ابحث بالاسم أو البريد أو المدرسة"
              value={searchInput}
            />
          </label>

          <label className="field">
            <span>الدور</span>
            <select onChange={(event) => setRoleFilter(event.target.value)} value={roleFilter}>
              <option value="">كل الأدوار</option>
              {roles.map((role) => (
                <option key={role.id} value={role.name}>
                  {role.display_name_ar}
                </option>
              ))}
            </select>
          </label>

          <label className="field">
            <span>الحالة</span>
            <select onChange={(event) => setStatusFilter(event.target.value)} value={statusFilter}>
              <option value="">الكل</option>
              <option value="active">نشط</option>
              <option value="inactive">غير نشط</option>
            </select>
          </label>
        </div>

        {loading ? <div className="loading-box">جارٍ تحميل الحسابات...</div> : null}
        {!loading ? <DataTable columns={columns} rows={rows} emptyMessage="لا توجد حسابات حالية." /> : null}
      </section>
      ) : null}

      {activeSection === "form" ? (
      <section className="surface-card page-stack" ref={formSectionRef}>
        <div className="page-header">
          <div>
            <span className="eyebrow">{editingUser ? "تعديل" : "إنشاء"}</span>
            <h3>{editingUser ? `تعديل الحساب: ${editingUser.full_name}` : "إنشاء حساب جديد"}</h3>
          </div>
          {editingUser ? (
            <button className="button button-secondary" onClick={() => selectUserForPermissions(editingUser)} type="button">
              إدارة صلاحيات هذا الحساب
            </button>
          ) : null}
        </div>

        <form
          className="page-stack"
          onSubmit={handleUserFormSubmit(async (values) => {
            setSavingUser(true);
            setError(null);
            setSuccessMessage(null);

            try {
              const payload = toUserPayload(values, editingUser);

              if (editingUser) {
                await userService.update(editingUser.id, payload);
                setSuccessMessage("تم تحديث الحساب بنجاح.");
              } else {
                await userService.create(payload);
                setSuccessMessage("تم إنشاء الحساب بنجاح.");
              }

              setEditingUser(null);
              resetUserForm(defaultUserForm);

              const refreshed = await userService.list({
                per_page: 100,
                "filter[search]": searchInput.trim() || undefined,
                "filter[role]": roleFilter || undefined,
                "filter[status]": statusFilter || undefined
              });

              setRows(refreshed.data);
            } catch (saveError) {
              setError(getErrorMessage(saveError));
            } finally {
              setSavingUser(false);
            }
          })}
        >
          <div className="grid-two">
            <label className="field">
              <span>الاسم</span>
              <input
                required
                {...registerUserForm("full_name")}
              />
              {userFormErrors.full_name ? <small className="field-hint">{userFormErrors.full_name.message}</small> : null}
            </label>

            <label className="field">
              <span>البريد</span>
              <input
                type="email"
                {...registerUserForm("email")}
              />
              {userFormErrors.email ? <small className="field-hint">{userFormErrors.email.message}</small> : null}
            </label>
          </div>

          <div className="grid-two">
            <label className="field">
              <span>الجوال</span>
              <input
                {...registerUserForm("phone")}
              />
            </label>

            <label className="field">
              <span>الدور</span>
              <select
                value={userForm.role}
                {...registerUserForm("role")}
              >
                {roles.map((role) => (
                  <option key={role.id} value={role.name}>
                    {role.display_name_ar}
                  </option>
                ))}
              </select>
            </label>
          </div>

          <div className="grid-two">
            <label className="field">
              <span>{isSupervisorRole ? "المدرسة الأساسية" : "المدرسة"}</span>
              <select
                value={userForm.school_id}
                {...registerUserForm("school_id")}
              >
                <option value="">بدون مدرسة مباشرة</option>
                {schools.map((school) => (
                  <option key={school.id} value={school.id}>
                    {school.name}
                  </option>
                ))}
              </select>
            </label>

            <label className="field">
              <span>{editingUser ? "كلمة مرور جديدة" : "كلمة المرور"}</span>
              <input
                placeholder={editingUser ? "اتركه فارغًا إن لم ترغب بتغييرها" : "Password@123"}
                required={!editingUser}
                type="password"
                {...registerUserForm("password")}
              />
            </label>
          </div>

          {isSupervisorRole ? (
            <div className="field">
              <span>المدارس المرتبطة بالمشرف</span>
              <div className="checkbox-dropdown" ref={supervisorSchoolsDropdownRef}>
                <button
                  aria-expanded={isSupervisorSchoolsDropdownOpen}
                  className="checkbox-dropdown-trigger"
                  onClick={() => setIsSupervisorSchoolsDropdownOpen((current) => !current)}
                  type="button"
                >
                  {userForm.school_ids.length
                    ? `تم اختيار ${userForm.school_ids.length} مدرسة`
                    : "اختر المدارس المشرف عليها"}
                  <span className="checkbox-dropdown-trigger-icon">{isSupervisorSchoolsDropdownOpen ? "▴" : "▾"}</span>
                </button>
                {isSupervisorSchoolsDropdownOpen ? (
                  <div className="checkbox-dropdown-panel">
                    <div className="dropdown-header">
                      <span>اختر المدارس</span>
                      <button
                        aria-label="إغلاق قائمة المدارس"
                        className="dropdown-close-button"
                        onClick={() => setIsSupervisorSchoolsDropdownOpen(false)}
                        type="button"
                      >
                        ×
                      </button>
                    </div>
                    <input
                      className="dropdown-search-input"
                      onChange={(event) => setSupervisorSchoolSearch(event.target.value)}
                      placeholder="ابحث عن مدرسة"
                      value={supervisorSchoolSearch}
                    />
                    <div className="selection-list">
                      {filteredSupervisorSchools.map((school) => {
                        const isChecked = userForm.school_ids.includes(String(school.id));

                        return (
                          <label className="selection-option" key={school.id}>
                            <input
                              checked={isChecked}
                              onChange={(event) => {
                                const nextSchoolIds = event.target.checked
                                  ? [...userForm.school_ids, String(school.id)]
                                  : userForm.school_ids.filter((schoolId) => schoolId !== String(school.id));

                                setUserFormValue("school_ids", nextSchoolIds, { shouldValidate: true });
                                setUserFormValue("school_id", nextSchoolIds[0] ?? "");
                              }}
                              type="checkbox"
                            />
                            <span>{school.name}</span>
                          </label>
                        );
                      })}
                      {!filteredSupervisorSchools.length ? (
                        <div className="field-hint">لا توجد مدارس مطابقة لنتيجة البحث.</div>
                      ) : null}
                    </div>
                    <div className="dropdown-footer">
                      <button
                        className="button button-primary dropdown-confirm"
                        onClick={() => setIsSupervisorSchoolsDropdownOpen(false)}
                        type="button"
                      >
                        تم
                      </button>
                    </div>
                  </div>
                ) : null}
              </div>
              {userFormErrors.school_ids ? <small className="field-hint">{userFormErrors.school_ids.message}</small> : null}
              <small className="field-hint">اختر مدرسة واحدة أو أكثر، وسيتم تقييد حساب المشرف بها فقط.</small>
            </div>
          ) : null}

          <label className="checkbox-row">
            <input
              checked={userForm.must_change_password}
              type="checkbox"
              {...registerUserForm("must_change_password")}
            />
            <span>إجبار المستخدم على تغيير كلمة المرور عند أول دخول</span>
          </label>

          <div className="button-row">
            <button className="button button-primary" disabled={savingUser} type="submit">
              {savingUser ? "جارٍ الحفظ..." : editingUser ? "حفظ التعديلات" : "إنشاء الحساب"}
            </button>
            <button
              className="button button-ghost"
              onClick={() => {
                setEditingUser(null);
                resetUserForm(defaultUserForm);
                setError(null);
              }}
              type="button"
            >
              إلغاء
            </button>
          </div>
        </form>
      </section>
      ) : null}

      {activeSection === "permissions" ? (
      <section className="surface-card page-stack" ref={permissionsSectionRef}>
        <div className="page-header">
          <div>
            <span className="eyebrow">الصلاحيات</span>
            <h3>إدارة صلاحيات الأدوار</h3>
          </div>
        </div>

        <label className="field">
          <span>اختر الدور</span>
          <select onChange={(event) => setSelectedRoleName(event.target.value)} value={selectedRoleName}>
            {roles.map((role) => (
              <option key={role.id} value={role.name}>
                {role.display_name_ar}
              </option>
            ))}
          </select>
        </label>

        {selectedRole ? (
          <div className="field">
            <span>الحسابات التابعة لهذا الدور</span>
            <div className="checkbox-dropdown" ref={roleUsersDropdownRef}>
              <button
                aria-expanded={isRoleUsersDropdownOpen}
                className="checkbox-dropdown-trigger"
                onClick={() => setIsRoleUsersDropdownOpen((current) => !current)}
                type="button"
              >
                {selectedRoleUserIds.length
                  ? isAllRoleUsersSelected
                    ? "جميع الحسابات التابعة للدور"
                    : `تم اختيار ${selectedRoleUserIds.length} حساب`
                  : "بدون تحديد: سيتم التعامل مع صلاحيات الدور فقط"}
                <span className="checkbox-dropdown-trigger-icon">{isRoleUsersDropdownOpen ? "▴" : "▾"}</span>
              </button>
              {isRoleUsersDropdownOpen ? (
                <div className="checkbox-dropdown-panel">
                  <div className="dropdown-header">
                    <span>اختر الحسابات</span>
                    <button
                      aria-label="إغلاق قائمة الحسابات"
                      className="dropdown-close-button"
                      onClick={() => setIsRoleUsersDropdownOpen(false)}
                      type="button"
                    >
                      ×
                    </button>
                  </div>
                  <input
                    className="dropdown-search-input"
                    onChange={(event) => setRoleUsersSearch(event.target.value)}
                    placeholder="ابحث عن حساب داخل هذا الدور"
                    value={roleUsersSearch}
                  />
                  <div className="selection-list">
                    <label className="selection-option">
                      <input
                        checked={isAllRoleUsersSelected}
                        onChange={(event) => {
                          setSelectedRoleUserIds(event.target.checked ? ["__all__"] : []);
                        }}
                        type="checkbox"
                      />
                      <span>الكل</span>
                    </label>
                    {filteredRoleUsers.map((roleUser) => {
                      const roleUserId = String(roleUser.id);
                      const isChecked = isAllRoleUsersSelected || selectedRoleUserIds.includes(roleUserId);

                      return (
                        <label className="selection-option" key={roleUser.id}>
                          <input
                            checked={isChecked}
                            disabled={isAllRoleUsersSelected}
                            onChange={(event) => {
                              setSelectedRoleUserIds((current) => {
                                const nextUserIds = current.filter((item) => item !== "__all__");

                                return event.target.checked
                                  ? [...nextUserIds, roleUserId]
                                  : nextUserIds.filter((item) => item !== roleUserId);
                              });
                            }}
                            type="checkbox"
                          />
                          <span>
                            {roleUser.full_name}
                            {roleUser.school?.name_ar ? ` - ${roleUser.school.name_ar}` : ""}
                          </span>
                        </label>
                      );
                    })}
                    {!filteredRoleUsers.length ? (
                      <div className="field-hint">لا توجد حسابات مطابقة لهذا الدور أو لنتيجة البحث.</div>
                    ) : null}
                  </div>
                  <div className="dropdown-footer">
                    <button
                      className="button button-primary dropdown-confirm"
                      onClick={() => setIsRoleUsersDropdownOpen(false)}
                      type="button"
                    >
                      تم
                    </button>
                  </div>
                </div>
              ) : null}
            </div>
            <small className="field-hint">
              إذا تركت هذه القائمة فارغة فسيتم تحديث صلاحيات الدور بالكامل فقط. إذا اخترت حسابات أو
              "الكل" فسيتم حفظ طبقة صلاحيات إضافية فوق الدور.
            </small>
          </div>
        ) : null}

        {selectedRole ? (
          <div className="permission-module-grid">
            {permissionModules.map((moduleGroup) => (
              <section className="permission-module-card" key={moduleGroup.module}>
                <div className="permission-module-head">
                  <h4>{getPermissionModuleLabel(moduleGroup.module)}</h4>
                  <label className="checkbox-row">
                    <input
                      checked={moduleGroup.permissions.every((permission) => selectedPermissionKeys.includes(permission.key))}
                      onChange={(event) => {
                        const modulePermissionKeys = moduleGroup.permissions.map((permission) => permission.key);

                        setSelectedPermissionKeys((current) => {
                          if (event.target.checked) {
                            return Array.from(new Set([...current, ...modulePermissionKeys]));
                          }

                          return current.filter((permissionKey) => !modulePermissionKeys.includes(permissionKey));
                        });
                      }}
                      type="checkbox"
                    />
                    <span>الكل</span>
                  </label>
                </div>
                <div className="permission-list">
                  {moduleGroup.permissions.map((permission) => (
                    <label className="checkbox-row" key={permission.key}>
                      <input
                        checked={selectedPermissionKeys.includes(permission.key)}
                        onChange={(event) => {
                          setSelectedPermissionKeys((current) =>
                            event.target.checked
                              ? [...current, permission.key]
                              : current.filter((item) => item !== permission.key)
                          );
                        }}
                        type="checkbox"
                      />
                      <span>{permission.display_name_ar}</span>
                    </label>
                  ))}
                </div>
              </section>
            ))}
          </div>
        ) : null}

        <div className="button-row">
          <button
            className="button button-primary"
            disabled={!selectedRole || savingPermissions}
            onClick={async () => {
              if (!selectedRole) {
                return;
              }

              setSavingPermissions(true);
              setError(null);
              setSuccessMessage(null);

              try {
                await userService.updateRolePermissions(selectedRole.id, selectedPermissionKeys);
                const refreshedRoles = await userService.listRoles();
                setRoles(refreshedRoles);
                setSuccessMessage(`تم تحديث صلاحيات الدور: ${selectedRole.display_name_ar}`);
              } catch (saveError) {
                setError(getErrorMessage(saveError));
              } finally {
                setSavingPermissions(false);
              }
            }}
            type="button"
          >
            {savingPermissions ? "جارٍ حفظ الصلاحيات..." : "حفظ صلاحيات الدور"}
          </button>
          <button
            className="button button-secondary"
            disabled={!selectedRole || savingPermissions || selectedRoleUserIds.length === 0}
            onClick={async () => {
              if (!selectedRole || selectedRoleUserIds.length === 0) {
                return;
              }

              setSavingPermissions(true);
              setError(null);
              setSuccessMessage(null);

              try {
                const updatedUsers = await userService.updateUserPermissions(selectedRole.id, {
                  permission_keys: selectedPermissionKeys,
                  apply_to_all: isAllRoleUsersSelected,
                  user_ids: isAllRoleUsersSelected
                    ? []
                    : selectedRoleUserIds.map((userId) => Number(userId)).filter((userId) => userId > 0)
                });

                setRoleUsers(updatedUsers);
                setSuccessMessage(
                  isAllRoleUsersSelected
                    ? `تم تحديث صلاحيات جميع الحسابات التابعة لدور: ${selectedRole.display_name_ar}`
                    : `تم تحديث صلاحيات الحسابات المحددة ضمن دور: ${selectedRole.display_name_ar}`
                );
              } catch (saveError) {
                setError(getErrorMessage(saveError));
              } finally {
                setSavingPermissions(false);
              }
            }}
            type="button"
          >
            {savingPermissions ? "جارٍ حفظ صلاحيات الحسابات..." : "حفظ صلاحيات الحسابات المحددة"}
          </button>
        </div>
      </section>
      ) : null}

      {selectedUserDetails ? (
        <div className="modal-backdrop" role="presentation">
          <section aria-modal="true" className="modal-card modal-card-narrow" role="dialog">
            <div className="page-header">
              <div>
                <span className="eyebrow">بيانات الحساب</span>
                <h3>{selectedUserDetails.full_name}</h3>
              </div>
              <button className="button button-ghost" onClick={() => setSelectedUserDetails(null)} type="button">
                إغلاق
              </button>
            </div>
            <div className="details-grid">
              <div>
                <span className="detail-label">الاسم</span>
                <strong>{selectedUserDetails.full_name}</strong>
              </div>
              <div>
                <span className="detail-label">البريد</span>
                <strong>{selectedUserDetails.email ?? "-"}</strong>
              </div>
              <div>
                <span className="detail-label">الدور</span>
                <strong>{selectedUserDetails.role_display_name_ar ?? getRoleLabel(selectedUserDetails.role)}</strong>
              </div>
              <div>
                <span className="detail-label">المدرسة</span>
                <strong>{selectedUserDetails.school?.name_ar ?? selectedUserDetails.assigned_schools?.map((school) => school.name_ar).join("، ") ?? "-"}</strong>
              </div>
              <div>
                <span className="detail-label">الحالة</span>
                <strong>{selectedUserDetails.status === "active" ? "نشط" : "غير نشط"}</strong>
              </div>
            </div>
          </section>
        </div>
      ) : null}
    </section>
  );
}
