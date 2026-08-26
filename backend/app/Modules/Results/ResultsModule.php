<?php
declare(strict_types=1);

namespace App\Modules\Results;

use App\Core\BaseModule;

class ResultsModule extends BaseModule
{
    protected string $id = 'results';

    protected array $name = [
        'ar' => 'النتائج والتقديرات الأكاديمية',
        'en' => 'Academic Results & Grades',
    ];

    protected array $description = [
        'ar' => 'الاستعلام عن النتائج الأكاديمية وسجلات الدرجات ومحاكاة التسجيل الفصلي للطلاب.',
        'en' => 'Inquiry for student grades, academic terms, transcripts, and course registration simulation.',
    ];

    protected string $version = '1.0.0';

    /**
     * @var string[]
     */
    protected array $dependencies = [
        'academic-structure',
        'academic-services',
    ];

    /**
     * @var string[]
     */
    protected array $ownedTables = [
        'course_results',
        'academic_terms',
    ];

    public function __construct()
    {
        $this->routesPath = __DIR__ . '/Routes/api.php';
    }
}
