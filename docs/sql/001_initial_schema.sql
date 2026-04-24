CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

CREATE TABLE roles (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    display_name_ar VARCHAR(100) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE permissions (
    id BIGSERIAL PRIMARY KEY,
    key VARCHAR(100) NOT NULL UNIQUE,
    display_name_ar VARCHAR(150) NOT NULL,
    module VARCHAR(50) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE role_permissions (
    role_id BIGINT NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    permission_id BIGINT NOT NULL REFERENCES permissions(id) ON DELETE CASCADE,
    granted_at TIMESTAMP NOT NULL DEFAULT NOW(),
    PRIMARY KEY (role_id, permission_id)
);

CREATE TABLE schools (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID NOT NULL DEFAULT uuid_generate_v4() UNIQUE,
    name_ar VARCHAR(255) NOT NULL,
    name_en VARCHAR(255) NULL,
    ministry_code VARCHAR(50) UNIQUE NULL,
    region VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    district VARCHAR(100) NULL,
    address TEXT NULL,
    phone VARCHAR(30) NULL,
    email VARCHAR(150) NULL,
    latitude NUMERIC(10, 7) NULL,
    longitude NUMERIC(10, 7) NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    storage_quota_mb INTEGER NOT NULL DEFAULT 2048,
    principal_user_id BIGINT NULL,
    metadata JSONB NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMP NULL
);

CREATE TABLE academic_years (
    id BIGSERIAL PRIMARY KEY,
    school_id BIGINT NULL REFERENCES schools(id) ON DELETE SET NULL,
    name_ar VARCHAR(100) NOT NULL,
    starts_on DATE NOT NULL,
    ends_on DATE NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE disability_categories (
    id BIGSERIAL PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name_ar VARCHAR(150) NOT NULL,
    description TEXT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE education_programs (
    id BIGSERIAL PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name_ar VARCHAR(150) NOT NULL,
    description TEXT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID NOT NULL DEFAULT uuid_generate_v4() UNIQUE,
    role_id BIGINT NOT NULL REFERENCES roles(id),
    school_id BIGINT NULL REFERENCES schools(id) ON DELETE SET NULL,
    full_name VARCHAR(255) NOT NULL,
    national_id_encrypted TEXT NULL,
    email VARCHAR(150) UNIQUE NULL,
    phone VARCHAR(30) UNIQUE NULL,
    password_hash VARCHAR(255) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    last_login_at TIMESTAMP NULL,
    last_login_ip INET NULL,
    locale VARCHAR(10) NOT NULL DEFAULT 'ar',
    must_change_password BOOLEAN NOT NULL DEFAULT FALSE,
    two_factor_enabled BOOLEAN NOT NULL DEFAULT FALSE,
    profile_photo_file_id BIGINT NULL,
    metadata JSONB NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMP NULL
);

ALTER TABLE schools
ADD CONSTRAINT fk_schools_principal_user
FOREIGN KEY (principal_user_id) REFERENCES users(id) ON DELETE SET NULL;

CREATE TABLE user_school_assignments (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    school_id BIGINT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
    assignment_type VARCHAR(30) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE students (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID NOT NULL DEFAULT uuid_generate_v4() UNIQUE,
    school_id BIGINT NOT NULL REFERENCES schools(id),
    academic_year_id BIGINT NULL REFERENCES academic_years(id) ON DELETE SET NULL,
    education_program_id BIGINT NULL REFERENCES education_programs(id) ON DELETE SET NULL,
    disability_category_id BIGINT NULL REFERENCES disability_categories(id) ON DELETE SET NULL,
    primary_teacher_user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    first_name VARCHAR(100) NOT NULL,
    father_name VARCHAR(100) NULL,
    grandfather_name VARCHAR(100) NULL,
    family_name VARCHAR(100) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    national_id_encrypted TEXT NULL,
    student_number VARCHAR(50) NULL,
    gender VARCHAR(10) NOT NULL,
    birth_date DATE NULL,
    grade_level VARCHAR(50) NULL,
    classroom VARCHAR(50) NULL,
    enrollment_status VARCHAR(20) NOT NULL DEFAULT 'active',
    medical_notes JSONB NULL,
    social_notes JSONB NULL,
    transportation_notes TEXT NULL,
    joined_at DATE NULL,
    archived_at TIMESTAMP NULL,
    metadata JSONB NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMP NULL
);

CREATE TABLE student_guardians (
    id BIGSERIAL PRIMARY KEY,
    student_id BIGINT NOT NULL REFERENCES students(id) ON DELETE CASCADE,
    parent_user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    relationship VARCHAR(30) NOT NULL,
    is_primary BOOLEAN NOT NULL DEFAULT FALSE,
    can_view_reports BOOLEAN NOT NULL DEFAULT TRUE,
    can_message_school BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE teacher_student_assignments (
    id BIGSERIAL PRIMARY KEY,
    school_id BIGINT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
    teacher_user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    student_id BIGINT NOT NULL REFERENCES students(id) ON DELETE CASCADE,
    assigned_by_user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    assignment_role VARCHAR(30) NOT NULL DEFAULT 'primary',
    starts_on DATE NULL,
    ends_on DATE NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE portfolios (
    id BIGSERIAL PRIMARY KEY,
    school_id BIGINT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
    owner_user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    student_id BIGINT NULL REFERENCES students(id) ON DELETE SET NULL,
    type VARCHAR(30) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    completion_rate NUMERIC(5,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE files (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID NOT NULL DEFAULT uuid_generate_v4() UNIQUE,
    school_id BIGINT NULL REFERENCES schools(id) ON DELETE SET NULL,
    uploaded_by_user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    related_type VARCHAR(100) NULL,
    related_id BIGINT NULL,
    category VARCHAR(30) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    storage_name VARCHAR(255) NOT NULL UNIQUE,
    storage_disk VARCHAR(50) NOT NULL,
    storage_path TEXT NOT NULL,
    mime_type VARCHAR(150) NOT NULL,
    extension VARCHAR(20) NULL,
    size_bytes BIGINT NOT NULL,
    checksum_sha256 VARCHAR(64) NULL,
    is_sensitive BOOLEAN NOT NULL DEFAULT FALSE,
    visibility VARCHAR(20) NOT NULL DEFAULT 'private',
    uploaded_at TIMESTAMP NOT NULL DEFAULT NOW(),
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMP NULL
);

CREATE TABLE portfolio_items (
    id BIGSERIAL PRIMARY KEY,
    portfolio_id BIGINT NOT NULL REFERENCES portfolios(id) ON DELETE CASCADE,
    school_id BIGINT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    item_type VARCHAR(30) NOT NULL,
    description TEXT NULL,
    event_date DATE NULL,
    file_id BIGINT NULL REFERENCES files(id) ON DELETE SET NULL,
    created_by_user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE iep_templates (
    id BIGSERIAL PRIMARY KEY,
    disability_category_id BIGINT NULL REFERENCES disability_categories(id) ON DELETE SET NULL,
    education_program_id BIGINT NULL REFERENCES education_programs(id) ON DELETE SET NULL,
    title VARCHAR(255) NOT NULL,
    template_schema JSONB NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_by_user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE iep_plans (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID NOT NULL DEFAULT uuid_generate_v4() UNIQUE,
    school_id BIGINT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
    student_id BIGINT NOT NULL REFERENCES students(id) ON DELETE CASCADE,
    academic_year_id BIGINT NULL REFERENCES academic_years(id) ON DELETE SET NULL,
    teacher_user_id BIGINT NOT NULL REFERENCES users(id),
    principal_user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    supervisor_user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    current_version_number INTEGER NOT NULL DEFAULT 1,
    status VARCHAR(30) NOT NULL DEFAULT 'draft',
    title VARCHAR(255) NOT NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    summary TEXT NULL,
    strengths TEXT NULL,
    needs TEXT NULL,
    accommodations JSONB NULL,
    generated_pdf_file_id BIGINT NULL REFERENCES files(id) ON DELETE SET NULL,
    submitted_at TIMESTAMP NULL,
    approved_at TIMESTAMP NULL,
    rejected_at TIMESTAMP NULL,
    rejection_reason TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMP NULL
);

CREATE TABLE iep_plan_versions (
    id BIGSERIAL PRIMARY KEY,
    iep_plan_id BIGINT NOT NULL REFERENCES iep_plans(id) ON DELETE CASCADE,
    school_id BIGINT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
    version_number INTEGER NOT NULL,
    content_json JSONB NOT NULL,
    change_summary TEXT NULL,
    created_by_user_id BIGINT NOT NULL REFERENCES users(id),
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    UNIQUE (iep_plan_id, version_number)
);

CREATE TABLE iep_plan_goals (
    id BIGSERIAL PRIMARY KEY,
    iep_plan_id BIGINT NOT NULL REFERENCES iep_plans(id) ON DELETE CASCADE,
    school_id BIGINT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
    domain VARCHAR(100) NOT NULL,
    goal_text TEXT NOT NULL,
    measurement_method TEXT NULL,
    baseline_value VARCHAR(100) NULL,
    target_value VARCHAR(100) NULL,
    due_date DATE NULL,
    sort_order INTEGER NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE iep_plan_comments (
    id BIGSERIAL PRIMARY KEY,
    iep_plan_id BIGINT NOT NULL REFERENCES iep_plans(id) ON DELETE CASCADE,
    school_id BIGINT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
    author_user_id BIGINT NOT NULL REFERENCES users(id),
    target_section VARCHAR(100) NULL,
    comment_text TEXT NOT NULL,
    is_internal BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE iep_plan_approvals (
    id BIGSERIAL PRIMARY KEY,
    iep_plan_id BIGINT NOT NULL REFERENCES iep_plans(id) ON DELETE CASCADE,
    school_id BIGINT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
    action_by_user_id BIGINT NOT NULL REFERENCES users(id),
    action_role VARCHAR(30) NOT NULL,
    from_status VARCHAR(30) NULL,
    to_status VARCHAR(30) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE student_reports (
    id BIGSERIAL PRIMARY KEY,
    school_id BIGINT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
    student_id BIGINT NOT NULL REFERENCES students(id) ON DELETE CASCADE,
    teacher_user_id BIGINT NOT NULL REFERENCES users(id),
    report_type VARCHAR(30) NOT NULL,
    report_period_label VARCHAR(100) NOT NULL,
    content_json JSONB NOT NULL,
    summary TEXT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'draft',
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE file_access_tokens (
    id BIGSERIAL PRIMARY KEY,
    file_id BIGINT NOT NULL REFERENCES files(id) ON DELETE CASCADE,
    token_hash VARCHAR(255) NOT NULL,
    issued_to_user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    expires_at TIMESTAMP NOT NULL,
    consumed_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE supervision_templates (
    id BIGSERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    criteria_schema JSONB NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_by_user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE supervisor_visits (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID NOT NULL DEFAULT uuid_generate_v4() UNIQUE,
    school_id BIGINT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
    supervisor_user_id BIGINT NOT NULL REFERENCES users(id),
    template_id BIGINT NULL REFERENCES supervision_templates(id) ON DELETE SET NULL,
    visit_date DATE NOT NULL,
    visit_status VARCHAR(20) NOT NULL DEFAULT 'scheduled',
    agenda TEXT NULL,
    summary TEXT NULL,
    overall_score NUMERIC(5,2) NULL,
    next_follow_up_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE supervisor_visit_items (
    id BIGSERIAL PRIMARY KEY,
    visit_id BIGINT NOT NULL REFERENCES supervisor_visits(id) ON DELETE CASCADE,
    school_id BIGINT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
    criterion_key VARCHAR(100) NOT NULL,
    criterion_label VARCHAR(255) NOT NULL,
    score NUMERIC(5,2) NULL,
    remarks TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE supervisor_visit_recommendations (
    id BIGSERIAL PRIMARY KEY,
    visit_id BIGINT NOT NULL REFERENCES supervisor_visits(id) ON DELETE CASCADE,
    school_id BIGINT NOT NULL REFERENCES schools(id) ON DELETE CASCADE,
    recommendation_text TEXT NOT NULL,
    owner_user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    due_date DATE NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'open',
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE notifications (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID NOT NULL DEFAULT uuid_generate_v4() UNIQUE,
    school_id BIGINT NULL REFERENCES schools(id) ON DELETE SET NULL,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    type VARCHAR(50) NOT NULL,
    channel VARCHAR(20) NOT NULL,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    data JSONB NULL,
    read_at TIMESTAMP NULL,
    sent_at TIMESTAMP NULL,
    failed_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE messages (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID NOT NULL DEFAULT uuid_generate_v4() UNIQUE,
    school_id BIGINT NULL REFERENCES schools(id) ON DELETE SET NULL,
    thread_key VARCHAR(100) NOT NULL,
    sender_user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    subject VARCHAR(255) NULL,
    body TEXT NOT NULL,
    parent_message_id BIGINT NULL REFERENCES messages(id) ON DELETE SET NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE message_recipients (
    id BIGSERIAL PRIMARY KEY,
    message_id BIGINT NOT NULL REFERENCES messages(id) ON DELETE CASCADE,
    recipient_user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE audit_logs (
    id BIGSERIAL PRIMARY KEY,
    school_id BIGINT NULL REFERENCES schools(id) ON DELETE SET NULL,
    user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    action VARCHAR(100) NOT NULL,
    target_type VARCHAR(100) NULL,
    target_id BIGINT NULL,
    method VARCHAR(10) NULL,
    endpoint TEXT NULL,
    ip_address INET NULL,
    user_agent TEXT NULL,
    old_values JSONB NULL,
    new_values JSONB NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE login_attempts (
    id BIGSERIAL PRIMARY KEY,
    identifier VARCHAR(150) NOT NULL,
    ip_address INET NULL,
    user_agent TEXT NULL,
    success BOOLEAN NOT NULL,
    attempted_at TIMESTAMP NOT NULL DEFAULT NOW(),
    locked_until TIMESTAMP NULL
);

CREATE TABLE password_reset_otps (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    code_hash VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    consumed_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_users_role_school_status ON users(role_id, school_id, status);
CREATE INDEX idx_students_school_status_disability ON students(school_id, enrollment_status, disability_category_id);
CREATE INDEX idx_iep_plans_school_student_status ON iep_plans(school_id, student_id, status);
CREATE INDEX idx_files_related ON files(school_id, related_type, related_id);
CREATE INDEX idx_notifications_user_read_at ON notifications(user_id, read_at);
CREATE INDEX idx_messages_thread_key_created_at ON messages(thread_key, created_at);
CREATE INDEX idx_audit_logs_user_created_at ON audit_logs(user_id, created_at);
CREATE INDEX idx_login_attempts_identifier_attempted_at ON login_attempts(identifier, attempted_at);
CREATE INDEX idx_students_full_name_search ON students USING gin(to_tsvector('simple', full_name));
