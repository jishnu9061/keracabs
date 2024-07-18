<?php

/**
 * Created By: JISHNU T K
 * Date: 2024/07/11
 * Time: 12:25:05
 * Description: ContactController.php
 */

namespace App\Http\Controllers;

use App\Http\Constants\FileDestinations;

use App\Http\Helpers\Utilities\ToastrHelper;

use App\Models\Contact;

use App\Http\Requests\SendMailRequest;

use App\Mail\ContactMail;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;

class ContactController extends Controller
{
    /**
     * @return [type]
     */
    public function index()
    {
        $path = $this->getView('admin.contact.index');
        $contacts = Contact::select('id', 'name', 'phone', 'email', 'subject', 'message', 'created_at')->paginate(FileDestinations::PAGE);
        $para = ['contacts' => $contacts];
        $title = 'Contacts';
        return $this->renderView($path, $para, $title);
    }

    /**
     * @param SendMailRequest $request
     *
     * @return [type]
     */
    public function sendMail(SendMailRequest $request)
    {
        $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
            'date' => Carbon::now()->format('Y-m-d')
        ]);
        Mail::to('jishnuganesh27@gmail.com')->send(new ContactMail($contact));
        return Response::json(['success' => true]);
    }

    /**
     * @param Contact $contact
     *
     * @return [type]
     */
    public function delete(Contact $contact)
    {
        $contact->delete();
        ToastrHelper::success('Contact deleted successfully');
        return Response::json(['success' => 'Contact Deleted Successfully']);
    }
}
