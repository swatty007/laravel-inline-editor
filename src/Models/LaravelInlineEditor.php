<?php

namespace swatty007\LaravelInlineEditor\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LaravelInlineEditor
 * @package swatty007\LaravelInlineEditor\Models
 *
 * @property integer $id
 * @property string  $key
 * @property string  $content
 */
class LaravelInlineEditor extends Model
{
    public $table = 'laravel_inline_editor';

    protected $fillable = [
        'key', 'content'
    ];
    
    /**
     * Renders content of the given block
     * 
     * @param  string $key Target key in the given database table
     * @return string
     */
    public static function render($key)
    {
        // TODO Custom DB
        /** @var LaravelInlineEditor $block */
        $block = LaravelInlineEditor::where('key', $key)->first();

        if (!$block) {
            return '';
        }

        return $block->content;
    }

    public function setContentAttribute($value)
    {
        $this->attributes['content'] = $this->cleanupContent($value);
    }

    /**
     * Removes all unnecessary strings from input content
     * 
     * @param  string $string New block content
     * @return string
     */
    protected function cleanupContent($string)
    {
        $string = trim(str_replace('  ', ' ', str_replace('&nbsp;', ' ', $string)));

        $vueJsDebugStrings = ['<!--v-start-->', '<!--v-end-->', '<!--v-component-->'];
        $string = str_replace($vueJsDebugStrings, '', $string);

        return trim($string);
    }
}