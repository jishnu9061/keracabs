<?php

/**
 * Created By: JISHNU T K
 * Date: 2024/08/24
 * Time: 11:03:47
 * Description: AdminManagerController.php
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Manager;

use App\Http\Requests\ManagerStoreRequest;
use App\Http\Requests\ManagerUpdateRequest;

use App\Http\Helpers\Utilities\ToastrHelper;
use Illuminate\Support\Facades\Response;

class AdminManagerController extends Controller
{
    /**
     * @return [type]
     */
    public function index()
    {
        $path = $this->getView('admin.manager.index');
        $managers = Manager::select('id', 'name', 'user_name', 'password', 'contact')->get();
        $para = ['managers' => $managers];
        $title = 'Manager';
        return $this->renderView($path, $para, $title);
    }

    /**
     * @param ManagerStoreRequest $request
     *
     * @return [type]
     */
    public function store(ManagerStoreRequest $request)
    {
        Manager::create([
            'name' => $request->input('name'),
            'user_name' => $request->input('username'),
            'password' => bcrypt($request->input('password')),
            'contact' => $request->input('number'),
        ]);

        return Response::json(['success' => true]);
    }

    /**
     * @param Manager $manager
     *
     * @return [type]
     */
    public function edit(Manager $manager)
    {
        $path = $this->getView('admin.manager.edit');
        $para = ['manager' => $manager];
        $title = 'Edit Manager';
        return $this->renderView($path, $para, $title);
    }

    /**
     * @param Manager $manager
     * @param ManagerUpdateRequest $request
     *
     * @return [type]
     */
    public function update(Manager $manager, ManagerUpdateRequest $request)
    {
        $manager->update([
            'name' => $request->input('name'),
            'user_name' => $request->input('username'),
            'contact' => $request->input('contact'),
            'password' => bcrypt($request->input('password'))
        ]);
        ToastrHelper::success('Manager updated successfully');
        return redirect()->route('manager.index');
    }

    /**
     * @param Manager $manager
     *
     * @return [type]
     */
    public function delete(Manager $manager)
    {
        $manager->delete();
        ToastrHelper::success('Manager deleted successfully');
        return Response::json(['success' => true]);
    }
}
