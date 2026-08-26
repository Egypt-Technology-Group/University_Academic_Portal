<?php
declare(strict_types=1);

namespace App\Modules\Admissions;

use App\Core\BaseModule;

class AdmissionsModule extends BaseModule
{
    protected string $id = 'admissions';

    protected array $name = [
        'ar' => 'القبول والتسجيل',
        'en' => 'Admissions & Registration',
    ];

    protected array $description = [
        'ar' => 'إدارة دورات القبول، وتقديم طلبات الالتحاق الإلكترونية، ومتابعة حالة الطلاب الجدد والتحقق من المستندات.',
        'en' => 'Management of admission cycles, online student applications, application tracking, and document verification.',
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
        'admission_cycles',
        'applications',
        'application_documents',
    ];

    public function __construct()
    {
        $this->routesPath = __DIR__ . '/Routes/api.php';
    }
}