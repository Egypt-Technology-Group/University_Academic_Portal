<?php
declare(strict_types=1);

namespace App\Modules\Documents;

use App\Core\BaseModule;

class DocumentsModule extends BaseModule
{
    protected string $id = 'documents';

    protected array $name = [
        'ar' => 'مركز الوثائق واللوائح',
        'en' => 'Documents & Regulations Repository',
    ];

    protected array $description = [
        'ar' => 'إدارة ونشر اللوائح والوثائق الرسمية والأدلة الإرشادية القابلة للتحميل.',
        'en' => 'Management and distribution of downloadable university documents, bylaws, and guidelines.',
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
        'download_documents',
    ];

    public function __construct()
    {
        $this->routesPath = __DIR__ . '/Routes/api.php';
    }
}
