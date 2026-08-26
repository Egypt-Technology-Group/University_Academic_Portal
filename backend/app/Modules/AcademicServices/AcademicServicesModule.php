<?php
declare(strict_types=1);

namespace App\Modules\AcademicServices;

use App\Core\BaseModule;

class AcademicServicesModule extends BaseModule
{
    protected string $id = 'academic-services';

    protected array $name = [
        'ar' => 'الخدمات الأكاديمية والطلابية',
        'en' => 'Academic & Student Services',
    ];

    protected array $description = [
        'ar' => 'إدارة الخدمات الإلكترونية للطلاب، إصدار الإفادات والشهادات الرسمية، وجداول الامتحانات.',
        'en' => 'Management of student electronic requests, official statement issuance and verification, and examination schedules.',
    ];

    protected string $version = '1.0.0';

    /**
     * @var string[]
     */
    protected array $dependencies = [
        'academic-structure',
    ];

    /**
     * @var string[]
     */
    protected array $ownedTables = [
        'student_records',
        'student_service_requests',
        'exam_schedules',
        'official_statements',
    ];

    public function __construct()
    {
        $this->routesPath = __DIR__ . '/Routes/api.php';
    }
}
