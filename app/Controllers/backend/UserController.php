<?php

namespace App\Controllers\backend;

use URL,
    Auth,
    Input,
    Session,
    Request,
    Redirect,
    Validator;
use App\Controllers\backend\Controller;
use App\Model\backend\UserModel;
use App\Component\backend\UserComponent;

class UserController extends Controller {

  protected $page;

  public function __construct() {
    parent::__construct();
    $this->page = 'User';
  }

  protected function index() {
    $data = $this->setDataPageView('view');
    return view('backend/home', $data);
  }

  protected function add() {
    $data = $this->setDataPageView('add');
    return view('backend/home', $data);
  }

  protected function edit($id = '') {
    $data = $this->setDataPageView('edit', $id);
    return view('backend/home', $data);
  }

  protected function setDataPageView($subPage, $id = '') {
    $result = [
        'global' => $this->global,
        'page' => $this->page,
        'subPage' => $subPage,
        'title' => $this->page . $this->global['title'],
        'admin' => $this->admin,
        'data' => []
    ];
    if ($subPage === 'view') {
      $selectField = [
          'user_id', 
          'user_email', 
          'user_fullname', 
          'user_social',
          'user_status'
      ];
      $row = UserModel::select($selectField)
              ->get();
      $result['data']['rows'] = $row;
    } else if ($subPage === 'edit') {
      $query = UserModel::where('user_id', $id)
              ->orderBy('user_id', 'desc')
              ->first();
      $result['data']['row'] = $query;
    } else if ($subPage === 'add') {
      // do 
    }
    return $result;
  }

  protected function setMsgResponseRequest($id, $serviceName, $status = '') {
    $result = [
        'status' => false,
        'serviceName' => $serviceName,
        'result' => [
            'id' => $id
        ]
    ];
    if (!empty($status)) {
      $result['result']['status'] = $status;
    }
    return $result;
  }

  public function get() {
    $result['data'] = UserModel::all();
    return $result;
  }

  public function getStatus() {
    $id = Request::input('id');
    $status = Request::input('user_status');
    $result = $this->updateStatus($id, $status);
    return $result;
  }

  protected function updateStatus($id, $status) {
    $serviceName = 'user-status';
    $result = $this->setMsgResponseRequest($id, $serviceName, $status);
    $set = [
        'user_status' => $status
    ];
    $query = UserModel::where('user_id', $id)
            ->update($set);
    if ($query) {
      $result['status'] = true;
    }
    return $result;
  }

  public function getDeleteWhere() {
    $ids = Request::input('id');
    $result = $this->deleteWhere($ids);
    return $result;
  }

  protected function deleteWhere($ids) {
    $serviceName = 'delete-where';
    $result = $this->setMsgResponseRequest($ids, $serviceName);
    $query = UserModel::whereIn('user_id', $ids)
            ->delete();
    if ($query) {
      $result['status'] = true;
    }
    return $result;
  }

  public function getWhere() {
    $serviceName = 'get-where';
    $id = Request::input('id');
    $result = $this->setMsgResponseRequest($id, $serviceName);
    $query = UserModel::where('user_id', $id)
            ->orderBy('user_id', 'desc')
            ->first();
    if ($query) {
      $result['status'] = true;
      $result['result'] = $query;
    }
    return $result;
  }

  public function getUpdateWhere() {
    $serviceName = 'update-where';
    $id = Request::input('user_id');
    $result = $this->setMsgResponseRequest($id, $serviceName);
    $set = [
        'user_tel' => Request::input('user_tel'),
        'user_address' => Request::input('user_address'),
        'user_fullname' => Request::input('user_fullname')
    ];
    $query = UserModel::where('user_id', $id)
            ->update($set);
    $this->setStatusSessionFlashWhenQuery($query);
    if ($query) {
      $result['status'] = true;
    }
    return $result;
  }

  public function getAddFormWhere() {
    $inputs = Request::all();
    unset($inputs['type_post']);
    unset($inputs['_token']);
    $findEmail = UserModel::where('user_email', $inputs['user_email'])->first();
    if ( !empty($findEmail) ) {
      Session::flash('systemError', $this->global['msgStatus']['error']);
      return Redirect::route('user/add');
    }
    $query = new UserModel;
    foreach ($inputs as $key => $value) {
      $query[$key] = $value;
    }
    $query->save();
    $this->setStatusSessionFlashWhenQuery($query);
    if (Request::input('type_post') === "saveclose") {
      return Redirect::route('user/index');
    } else {
      return Redirect::route('user/add');
    }
  }

  public function getUpdateFormWhere() {
    $id = Request::input('user_id');
    $set = [
        'user_tel' => Request::input('user_tel'),
        'user_address' => Request::input('user_address'),
        'user_fullname' => Request::input('user_fullname')
    ];
    $query = UserModel::where('user_id', $id)
            ->update($set);
    $this->setStatusSessionFlashWhenQuery($query);
    if (Request::input('type_post') === "saveclose") {
      return Redirect::route('user/index');
    } else {
      return Redirect::route('user/edit', $id);
    }
  }

  protected function setStatusSessionFlashWhenQuery($query) {
    if ($query) {
      Session::flash('systemSuccess', $this->global['msgStatus']['success']);
    } else {
      Session::flash('systemError', $this->global['msgStatus']['error']);
    }
  }

}
