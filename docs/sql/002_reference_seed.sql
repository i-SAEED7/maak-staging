INSERT INTO roles (name, display_name_ar, description) VALUES
('super_admin', 'المدير العام للنظام', 'صلاحية كاملة على مستوى النظام'),
('admin', 'الإداري العام', 'صلاحيات مساندة وإدارية حسب الإعداد'),
('supervisor', 'المشرف التربوي', 'إشراف على مجموعة مدارس'),
('principal', 'مدير المدرسة', 'إدارة مدرسة واحدة'),
('teacher', 'المعلم', 'إدارة الطلاب والخطط والتقارير'),
('parent', 'ولي الأمر', 'الاطلاع على بيانات الأبناء والتواصل')
ON CONFLICT (name) DO NOTHING;

INSERT INTO education_programs (code, name_ar, description, is_active) VALUES
('yasir_learning', 'يسير التعليمي', 'برنامج دعم تعليمي لذوي الإعاقة', true),
('adhd_support', 'فرط حركة وتشتت انتباه', 'برنامج دعم مخصص للطلاب ذوي الإعاقة من فئة فرط الحركة وتشتت الانتباه', true)
ON CONFLICT (code) DO NOTHING;

INSERT INTO disability_categories (code, name_ar, description, is_active) VALUES
('learning_disabilities', 'صعوبات تعلم', 'برامج صعوبات التعلم', true),
('hearing_impairment', 'إعاقة سمعية', 'برامج الإعاقة السمعية', true),
('visual_impairment', 'إعاقة بصرية', 'برامج الإعاقة البصرية', true),
('autism', 'اضطراب طيف التوحد', 'برامج التوحد', true),
('intellectual_disability', 'إعاقة فكرية', 'برامج الإعاقة الفكرية', true),
('multiple_disabilities', 'إعاقات متعددة', 'برامج الإعاقات المتعددة', true),
('speech_disorders', 'اضطرابات النطق والكلام', 'دعم علاجي وتربوي', true),
('physical_disability', 'إعاقة حركية', 'الدعم الحركي والتكيفي', true)
ON CONFLICT (code) DO NOTHING;
