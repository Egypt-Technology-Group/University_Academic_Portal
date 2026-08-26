<?php
declare(strict_types=1);

namespace App\Modules\AcademicStructure;

use App\Core\BaseModule;

class AcademicStructureModule extends BaseModule
{
    protected string $id = 'academic-structure';

    protected array $name = [
        'ar' => 'الهيكل الأكاديمي',
        'en' => 'Academic Structure',
    ];

    protected array $description = [
        'ar' => 'إدارة الكليات والأقسام العلمية والبرامج الأكاديمية وأعضاء هيئة التدريس.',
        'en' => 'Management of colleges, academic departments, study programs, and faculty profiles.',
    ];

    protected string $version = '1.0.0';

    /**
     * @var string[]
     */
    protected array $dependencies = [];

    /**
     * @var string[]
     */
    protected array $ownedTables = [
        'colleges',
        'departments',
        'programs',
        'faculty_profiles',
    ];

    public function __construct()
    {
        $this->routesPath = __DIR__ . '/Routes/api.php';
    }
}