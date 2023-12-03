<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Articles extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'articles';

    public static function getFilteredData(Request $request){
        $source = $request['source'] ?? null;
        $author = $request['author'] ?? null;
        $publishedAt = $request['published_at'] ?? null;
        $category = $request['category'] ?? null;
        $query = Articles::query();
        $query = $source ? $query->where('api_source_id', 'like', '%' . $source . '%') : $query;
        $query = $author ? $query->where('author', 'like', '%' . $author . '%') : $query;
        $query = $publishedAt ? $query->whereDate('published_at', '=', $publishedAt) : $query;

        $result = collect($query->get())->map(function ($item) use ($category) {
            if ($item->api_source_id == 1) {
                if (str_contains($item->title, $category)) {
                    return $item;
                }

            } else if ($item->api_source_id == 2 && !empty($item->categories)) {
                $itemCategories = json_decode($item->categories, true);
                $key = in_array($category, $itemCategories, true);
                if ($key !== false) {
                    return $item;
                }
            }
        });

       return array_filter($result->toArray(), function ($value) {
            return $value !== null;
        });
    }
}
