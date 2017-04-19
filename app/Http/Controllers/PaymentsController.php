<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payments\CreateRequest;
use App\Http\Requests\Payments\UpdateRequest;
use App\Http\Requests\Payments\DeleteRequest;
use App\Http\Controllers\Controller;
use App\Entities\Payments\PaymentsRepository;

use Illuminate\Http\Request;

class PaymentsController extends Controller {

	private $repo;

	public function __construct(PaymentsRepository $repo)
	{
	    $this->repo = $repo;
	}

	public function index(Request $req)
	{
		$payments = $this->repo->getAll($req->get('q'));
		return view('payments.index',compact('payments'));
	}

	public function create()
	{
		$projects = $this->repo->getProjectsList();
		$customers = $this->repo->getCustomersAndVendorsList();
		return view('payments.create',compact('projects','customers'));
	}

	public function store(CreateRequest $req)
	{
		$payment = $this->repo->create($req->except('_token'));
		flash()->success(trans('payment.created'));
		return redirect()->route('projects.payments', $payment->project_id);
	}

	public function show($paymentId)
	{
		$payment = $this->repo->requireById($paymentId);
		return view('payments.show', compact('payment'));
	}

	public function edit($paymentId)
	{
		$payment = $this->repo->requireById($paymentId);
		$projects = $this->repo->getProjectsList();
		$customers = $this->repo->getCustomersList();
		return view('payments.edit',compact('payment','projects','customers'));
	}

	public function update(UpdateRequest $req, $paymentId)
	{
		$payment = $this->repo->update($req->except(['_method','_token']), $paymentId);
		flash()->success(trans('payment.updated'));
		return redirect()->route('payments.show', $paymentId);
	}

	public function delete($paymentId)
	{
	    $payment = $this->repo->requireById($paymentId);
		return view('payments.delete', compact('payment'));
	}

	public function destroy(DeleteRequest $req, $paymentId)
	{
		$payment = $this->repo->requireById($paymentId);
		$projectId = $payment->project_id;
		if ($paymentId == $req->get('payment_id'))
		{
			$payment->delete();
	        flash()->success(trans('payment.deleted'));
		}
		else
			flash()->error(trans('payment.undeleted'));

		return redirect()->route('projects.payments', $projectId);
	}

}
