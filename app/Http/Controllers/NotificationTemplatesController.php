<?php

namespace App\Http\Controllers;

use App\Models\NotificationTemplateLangs;
use App\Models\NotificationTemplates;
use App\Models\Utility;
use Illuminate\Http\Request;

class NotificationTemplatesController extends Controller
{
    public function index( $slug = '', $id = null, $lang = 'en',$type = 'slack')
    {
        // if(\Auth::user()->type == 'super admin' || \Auth::user()->type == 'company')
        // {
            // $types = NotificationTemplates::$types;
            $currentWorkspace = Utility::getWorkspaceBySlug($slug);

            if($id != null)
            {
                $notification_template     = NotificationTemplates::where('id',$id)->first();
            }
            else
            {
                $notification_template     = NotificationTemplates::first();
            }
            if(empty($notification_template))
            {
                return redirect()->back()->with('error', __('Not exists in notification template.'));
            }

            $languages         = Utility::languages();
            $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', $lang)->where('created_by', \Auth::user()->id)->first();

            if(!isset($curr_noti_tempLang) || empty($curr_noti_tempLang))
            {
                $curr_noti_tempLang       = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', $lang)->first();
            }

            if(!isset($curr_noti_tempLang) || empty($curr_noti_tempLang))
            {
                $curr_noti_tempLang       = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', 'en')->first();
                !empty($curr_noti_tempLang) ? $curr_noti_tempLang->lang = $lang : null;
            }

            $notification_templates = NotificationTemplates::get();

            return view('Notifications.index', compact('notification_template','notification_templates','curr_noti_tempLang','languages','type','currentWorkspace'));
        // }
        // else
        // {
        //     return redirect()->back()->with('error', __('Permission denied.'));
        // }
    }

    public function update(Request $request, $slug ,$id)
    {
        $validator = \Validator::make(
            $request->all(), [
                                'content' => 'required',
                            ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $NotiLangTemplate = NotificationTemplateLangs::where('parent_id', '=', $id)->where('lang', '=', $request->lang)->where('created_by', '=', \Auth::user()->id)->first();

        // if record not found then create new record else update it.
        if(empty($NotiLangTemplate))
        {
            $variables = NotificationTemplateLangs::where('parent_id', '=', $id)->where('lang', '=', $request->lang)->first()->variables;

            $NotiLangTemplate            = new NotificationTemplateLangs();
            $NotiLangTemplate->parent_id = $id;
            $NotiLangTemplate->lang      = $request['lang'];
            $NotiLangTemplate->content   = $request['content'];
            $NotiLangTemplate->variables = $variables;
            $NotiLangTemplate->created_by = \Auth::user()->id;
            $NotiLangTemplate->save();
        }
        else
        {
            $NotiLangTemplate->content = $request['content'];
            $NotiLangTemplate->save();
        }

        return redirect()->route(
            'notification-templates.index', [
                $slug,
                $id,
                $request->lang,
            ]
        )->with('success', __('Notification Template successfully updated.'));
    }
    public function show()
    {

    }
}
