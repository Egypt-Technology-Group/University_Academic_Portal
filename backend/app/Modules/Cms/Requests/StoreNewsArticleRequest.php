<?php
declare(strict_types=1);

namespace App\Modules\Cms\Requests;

use App\Modules\Cms\Models\NewsCategory;
use Illuminate\Foundation\Http\FormRequest;

class StoreNewsArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('category_id') && $this->filled('category')) {
            $cat = NewsCategory::where('slug', $this->input('category'))
                ->orWhere('id', $this->input('category'))
                ->first();
            if ($cat) {
                $this->merge(['category_id' => $cat->id]);
            } else {
                $firstCat = NewsCategory::first();
                if ($firstCat) {
                    $this->merge(['category_id' => $firstCat->id]);
                }
            }
        }

        if ($this->filled('summary_ar') && !$this->filled('excerpt_ar')) {
            $this->merge(['excerpt_ar' => $this->input('summary_ar')]);
        }
        if ($this->filled('summary_en') && !$this->filled('excerpt_en')) {
            $this->merge(['excerpt_en' => $this->input('summary_en')]);
        }
        if ($this->filled('content_ar') && !$this->filled('body_ar')) {
            $this->merge(['body_ar' => $this->input('content_ar')]);
        }
        if ($this->filled('content_en') && !$this->filled('body_en')) {
            $this->merge(['body_en' => $this->input('content_en')]);
        }
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:news_categories,id',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'excerpt_ar' => 'nullable|string',
            'excerpt_en' => 'nullable|string',
            'body_ar' => 'required|string',
            'body_en' => 'required|string',
            'featured_image' => 'nullable|string',
            'featured_image_file' => 'nullable|image|max:10240',
            'image' => 'nullable|image|max:10240',
            'is_featured' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ];
    }
}
