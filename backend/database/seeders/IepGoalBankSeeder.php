<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DisabilityCategory;
use App\Models\IepGoalBank;
use Illuminate\Database\Seeder;

/**
 * Seed the IEP Goal Bank with initial educational goals.
 *
 * Goals are organized by:
 *   - Disability category (فكرية، توحد، صعوبات تعلم، ...)
 *   - Domain (أكاديمي، سلوكي، اجتماعي، تواصلي، حركي، استقلالي)
 */
class IepGoalBankSeeder extends Seeder
{
    public function run(): void
    {
        $categories = DisabilityCategory::pluck('id', 'name_ar')->toArray();

        if (empty($categories)) {
            $this->command->warn('لم يتم العثور على فئات الإعاقة. يرجى تشغيل seeders الأساسية أولاً.');
            return;
        }

        $goals = $this->buildGoals($categories);

        foreach ($goals as $goal) {
            IepGoalBank::firstOrCreate(
                [
                    'disability_category_id' => $goal['disability_category_id'],
                    'domain' => $goal['domain'],
                    'goal_text' => $goal['goal_text'],
                ],
                $goal
            );
        }

        $this->command->info('تم بذر ' . count($goals) . ' هدفاً تعليمياً في بنك الأهداف.');
    }

    private function buildGoals(array $categories): array
    {
        $goals = [];
        $sortOrder = 0;

        // أهداف الإعاقة الفكرية
        $intellectualId = $categories['إعاقة فكرية'] ?? $categories['فكرية'] ?? array_values($categories)[0] ?? null;
        if ($intellectualId) {
            $goals = array_merge($goals, [
                [
                    'disability_category_id' => $intellectualId,
                    'domain' => 'أكاديمي',
                    'goal_text' => 'أن يتعرف الطالب على الحروف الهجائية (أ - ي) قراءة وكتابة بنسبة إتقان 80%',
                    'strategies' => ['البطاقات المصورة', 'التكرار والتعزيز', 'اللعب التعليمي', 'استخدام الحاسوب'],
                    'suggested_criteria' => ['نسبة الإتقان 80%', 'خلال 3 محاولات متتالية'],
                    'grade_level_min' => 1,
                    'grade_level_max' => 6,
                    'is_active' => true,
                    'sort_order' => ++$sortOrder,
                ],
                [
                    'disability_category_id' => $intellectualId,
                    'domain' => 'أكاديمي',
                    'goal_text' => 'أن يجمع الطالب الأعداد ضمن العدد 20 بدون حمل بنسبة إتقان 75%',
                    'strategies' => ['العداد', 'المكعبات', 'الخط العددي', 'أوراق العمل المبسطة'],
                    'suggested_criteria' => ['نسبة الإتقان 75%', '4 من أصل 5 محاولات'],
                    'grade_level_min' => 1,
                    'grade_level_max' => 6,
                    'is_active' => true,
                    'sort_order' => ++$sortOrder,
                ],
                [
                    'disability_category_id' => $intellectualId,
                    'domain' => 'استقلالي',
                    'goal_text' => 'أن يقوم الطالب بارتداء ملابسه بشكل مستقل خلال 5 دقائق',
                    'strategies' => ['النمذجة', 'التسلسل العكسي', 'التعزيز الإيجابي', 'الصور المتسلسلة'],
                    'suggested_criteria' => ['3 أيام متتالية', 'بدون مساعدة جسدية'],
                    'grade_level_min' => 1,
                    'grade_level_max' => 9,
                    'is_active' => true,
                    'sort_order' => ++$sortOrder,
                ],
                [
                    'disability_category_id' => $intellectualId,
                    'domain' => 'اجتماعي',
                    'goal_text' => 'أن يتفاعل الطالب مع أقرانه أثناء اللعب الجماعي لمدة 10 دقائق دون سلوك عدواني',
                    'strategies' => ['اللعب الموجه', 'القصص الاجتماعية', 'التعزيز التفاضلي', 'تقليد الأقران'],
                    'suggested_criteria' => ['4 من أصل 5 فرص', 'ملاحظة مباشرة'],
                    'grade_level_min' => 1,
                    'grade_level_max' => 9,
                    'is_active' => true,
                    'sort_order' => ++$sortOrder,
                ],
            ]);
        }

        // أهداف التوحد
        $autismId = $categories['اضطراب طيف التوحد'] ?? $categories['توحد'] ?? null;
        if ($autismId) {
            $goals = array_merge($goals, [
                [
                    'disability_category_id' => $autismId,
                    'domain' => 'تواصلي',
                    'goal_text' => 'أن يستخدم الطالب جملة من 3 كلمات للتعبير عن احتياجاته في 8 من أصل 10 فرص',
                    'strategies' => ['التواصل بالصور (PECS)', 'النمذجة اللغوية', 'التعزيز الفوري', 'الأجهزة المساعدة'],
                    'suggested_criteria' => ['8 من أصل 10 فرص', 'عبر أسبوعين متتاليين'],
                    'grade_level_min' => 1,
                    'grade_level_max' => 9,
                    'is_active' => true,
                    'sort_order' => ++$sortOrder,
                ],
                [
                    'disability_category_id' => $autismId,
                    'domain' => 'سلوكي',
                    'goal_text' => 'أن يلتزم الطالب بالروتين اليومي للصف دون نوبات غضب لمدة 3 أيام متتالية',
                    'strategies' => ['الجدول البصري', 'المؤقت المرئي', 'الإنذار المبكر', 'منطقة الهدوء'],
                    'suggested_criteria' => ['3 أيام متتالية', 'توثيق السلوك يومياً'],
                    'grade_level_min' => 1,
                    'grade_level_max' => 12,
                    'is_active' => true,
                    'sort_order' => ++$sortOrder,
                ],
                [
                    'disability_category_id' => $autismId,
                    'domain' => 'اجتماعي',
                    'goal_text' => 'أن يبادر الطالب بالتواصل البصري عند مناداته باسمه في 7 من أصل 10 محاولات',
                    'strategies' => ['التحفيز البصري', 'التعزيز التفاضلي', 'الألعاب التفاعلية', 'اللعب الموازي'],
                    'suggested_criteria' => ['7 من 10 محاولات', 'خلال أسبوع'],
                    'grade_level_min' => 1,
                    'grade_level_max' => 6,
                    'is_active' => true,
                    'sort_order' => ++$sortOrder,
                ],
            ]);
        }

        // أهداف صعوبات التعلم
        $ldId = $categories['صعوبات تعلم'] ?? $categories['صعوبات التعلم'] ?? null;
        if ($ldId) {
            $goals = array_merge($goals, [
                [
                    'disability_category_id' => $ldId,
                    'domain' => 'أكاديمي',
                    'goal_text' => 'أن يقرأ الطالب نصاً مكوناً من 50 كلمة بطلاقة ودقة بنسبة 90%',
                    'strategies' => ['القراءة المتكررة', 'التهجئة الصوتية', 'القراءة المشتركة', 'الكتب المصورة'],
                    'suggested_criteria' => ['نسبة الدقة 90%', 'سرعة 40 كلمة/دقيقة'],
                    'grade_level_min' => 2,
                    'grade_level_max' => 6,
                    'is_active' => true,
                    'sort_order' => ++$sortOrder,
                ],
                [
                    'disability_category_id' => $ldId,
                    'domain' => 'أكاديمي',
                    'goal_text' => 'أن يميز الطالب بين الحركات القصيرة والطويلة أثناء القراءة بنسبة 85%',
                    'strategies' => ['التلوين التمييزي', 'البطاقات الوامضة', 'الألعاب الصوتية', 'التدريب المكثف'],
                    'suggested_criteria' => ['نسبة الإتقان 85%', '3 جلسات متتالية'],
                    'grade_level_min' => 1,
                    'grade_level_max' => 4,
                    'is_active' => true,
                    'sort_order' => ++$sortOrder,
                ],
                [
                    'disability_category_id' => $ldId,
                    'domain' => 'أكاديمي',
                    'goal_text' => 'أن يحل الطالب مسائل الضرب للأعداد من 1 إلى 5 بنسبة إتقان 80%',
                    'strategies' => ['جدول الضرب المصور', 'الأنماط العددية', 'التطبيقات التفاعلية', 'الألعاب الرقمية'],
                    'suggested_criteria' => ['نسبة الإتقان 80%', '4 من أصل 5 اختبارات'],
                    'grade_level_min' => 3,
                    'grade_level_max' => 6,
                    'is_active' => true,
                    'sort_order' => ++$sortOrder,
                ],
            ]);
        }

        return $goals;
    }
}
