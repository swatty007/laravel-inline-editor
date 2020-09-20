<?php

namespace swatty007\LaravelInlineEditor;

use swatty007\LaravelInlineEditor\Models\LaravelInlineEditor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class InlineEditor
{

    protected static $models = [];

    /**
     * Setup block
     * Checks if our target Object exists in our target DB
     *
     * @param string $source_value DB row, to look up the content.
     * @param string $table Allows to override the table, which will be searched for the given key.
     * @param string $source_key The key value of the database, for which we are looking.
     * @param string $target_key The key of the database, which should be updated.
     * @param string $options Display options for the vue editor.
     * @param string $validationRules Name of the Configuration Object for some Custom Validation Rules.
     * @param boolean $rawText Enables stripping of all HMTL Elements, from the input content.
     * @param string $lang Language Identifier used for localization.
     * @return boolean
     */
    public static function setUp( $source_value, $table = null, $source_key = null, $target_key = null, $options = null, $validationRules = null, $rawText = null, $lang = null)
    {
        // Set default values for out items, if they are not defined
        if( !isset($source_value) )     $source_value       = 'key';

        if( !isset($table) )            $table              = 'laravel_inline_editor';
        if( !isset($source_key) )       $source_key         = 'key';
        if( !isset($target_key) )       $target_key         = 'content';
        if( !isset($options) )          $options            = config('laravel-inline-editor.options');
        if( !isset($validationRules) )  $validationRules    = 'default';
        if( !isset($rawText) )          $rawText            = false;
        if( !isset($lang) )             $lang               = false;

        $rawText = $rawText ? 'true':'false';

        self::$models[$source_value] = [$table, $source_key, $target_key , $options, $validationRules, $rawText, $lang];

        ob_start();

        // Returns true, or false based on the existence of the Object in our target table
        return !! DB::table($table)->where( $source_key, $source_value )->first();
    }

    /**
     * Teardown block setup
     */
    public static function tearDown()
    {
        // Get the default content from our component
        $html = ob_get_clean();

        end(self::$models);                         // Get to the last item of our array
        $key        = key(self::$models);                 // get its key value
        $objectData = array_pop(self::$models);     // and object data

        // Get the content of our object, from the corresponding database
        $contentBlock = DB::table($objectData[0])->where( $objectData[1], $key)->first();

        // if no item in our db exists yet...
        if (!isset($contentBlock) ) {
            // ... create a new entry
            $contentBlock = new LaravelInlineEditor([
                'key'           => $key,
                'content'       => trim($html),
            ]);

            $contentBlock->save();
        }

        if (Gate::allows('laravel-inline-editor')) {

            return sprintf('<inline-content-block 
                source_key="'.$objectData[1].'" 
                source_value="'.$key.'"
                target_key="'.$objectData[2].'" 
                validationRules="'.$objectData[4] .'"
                rawText="'.$objectData[5] .'"
                lang="'.$objectData[6] .'"
                table="'.$objectData[0].'" 
                options="'. $objectData[3].'"
                content="'. str_replace('%', '%%', e($contentBlock->{$objectData[2]}) ) .'"
                >%s</inline-content-block>', $key, trim($contentBlock->{$objectData[2]}));
        }

        if( $objectData[6] ) {
            $data = json_decode( $contentBlock->{$objectData[2]} );
            if( isset($data->{$objectData[6]}) )
                $data = $data->{$objectData[6]};
            else
                $data = '';
            return $data;
        }
        else
            return trim($contentBlock->{$objectData[2]});
    }
}