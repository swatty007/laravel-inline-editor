<?php

namespace swatty007\LaravelInlineEditor\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;

class InlineController extends BaseController
{
    public function index(Request $request)
    {
        $blocks     = $request->blocks;

        foreach ($blocks as $block)
        {
            $rules = 'laravel-inline-editor.rules.' . $block['validationRules'];
            Validator::make($block, config($rules) )->validate();

            if($block['rawText'] == 'true')
                $content = trim(strip_tags($block['target_value']));
            else
                $content = trim( $block['target_value'] );

            DB::table($block['table'])
                ->where($block['source_key'], $block['source_value'])
                ->update([$block['target_key'] => $content]);
        }

        return 'ok';
    }
}