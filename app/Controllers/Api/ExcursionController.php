<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ExcursionModel;


class ExcursionController extends ResourceController
{
    public function addExcursion()
	{
		$rules = [
            "excursions_template_id"=>"required",
            "user_id"=>"required",
            "num_max_people"=>"required",
            "price"=>"required",
            "start"=>"required",
            "end"=>"required"
		];

		$messages = [
            "excursions_template_id" => [
				"required" => "excursions_template_id is required",
				
			],
			"user_id" => [
				"required" => "user is required",
				
			],

			"num_max_people" => [
				"required" => "num_max_people route is required",	
			],

			"price" => [
				"required" => "price is required"
			],

			"start" => [
				"required" => "start is required"
			],

            "end" => [
				"required" => "end is required"
			],
		];

		if (!$this->validate($rules, $messages)) {

			$response = [
				'status' => 500,
				'error' => true,
				'message' => $this->validator->getErrors(),
				'data' => []
			];
		} else {

			$exc = new ExcursionModel();

			$data['excursions_template_id'] = $this->request->getVar("excursions_template_id");
			$data['user_id'] = $this->request->getVar("user_id");
			$data['num_max_people'] = $this->request->getVar("num_max_people");
			$data['price'] = $this->request->getVar("price");
			$data['start'] = $this->request->getVar("start");
			$data['end'] = $this->request->getVar("end");

			$exc->save($data);

			$response = [
				'status' => 200,
				'error' => false,
				'message' => 'Excursion added successfully',
				'data' => []
			];
		}

		return $this->respond($response);
	}

	public function listExcursion()
	{
		$exc = new ExcursionModel();
            
        //log_message('error', $e->getMessage());
        
		$response = [
			'status' => 200,
			"error" => false,
			'message' => 'Excursion list',
			'data' => $exc->findAll()
		];

		return $this->respond($response);
	}

	public function showExcursion ($exc_id)
	{
		$exc = new ExcursionModel();

		$data = $exc->find($exc_id);
        //$data = $model->where(['id' => $exc_id])->first();

		if (!empty($data)) {

			$response = [
				'status' => 200,
				"error" => false,
				'messages' => 'Single excursion data',
				'data' => $data
			];

		} else {

			$response = [
				'status' => 500,
				"error" => true,
				'messages' => 'No excursion found',
				'data' => []
			];
		}

		return $this->respond($response);
	}

	public function updateExcursion ($exc_id)
	{
		$rules = [
            "excursions_template_id"=>"required",
            "user_id"=>"required",
            "num_max_people"=>"required",
            "price"=>"required",
            "start"=>"required",
            "end"=>"required"

		];

		$messages = [
            "excursions_template_id" => [
				"required" => "excursions_template_id is required",
				
			],
			"user_id" => [
				"required" => "user is required",
				
			],

			"num_max_people" => [
				"required" => "num_max_people route is required",	
			],

			"price" => [
				"required" => "price is required"
			],

			"start" => [
				"required" => "start is required"
			],

            "end" => [
				"required" => "end is required"
			],
		];


		if (!$this->validate($rules, $messages)) {

			$response = [
				'status' => 500,
				'error' => true,
				'message' => $this->validator->getErrors(),
				'data' => []
			];
		} else {

			$exc = new ExcursionModel();

			if ($exc->find($exc_id)) {

            $data['excursions_template_id'] = $this->request->getVar("excursions_template_id");
			$data['user_id'] = $this->request->getVar("user_id");
			$data['num_max_people'] = $this->request->getVar("num_max_people");
			$data['price'] = $this->request->getVar("price");
			$data['start'] = $this->request->getVar("start");
			$data['end'] = $this->request->getVar("end");

		
			    $exc->update($exc_id, $data);

				$response = [
					'status' => 200,
					'error' => false,
					'message' =>'Excursion updated successfully',
					'data' => []
				];
			}else {

				$response = [
					'status' => 500,
					"error" => true,
					'message' => 'No excursion found',
					'data' => []
				];
			}
		}

		return $this->respond($response);
	}

	public function deleteExcursion($exc_id)
	{
		$exc = new ExcursionModel();

		$data = $exc->find($exc_id);

		if (!empty($data)) {

			$exc->delete($exc_id);

			$response = [
				'status' => 200,
				"error" => false,
				'message' => 'Excursion deleted successfully',
				'data' => []
			];

		} else {

			$response = [
				'status' => 500,
				"error" => true,
				'message' => 'No excursionfound',
				'data' => []
			];
		}

		return $this->respond($response);
	}
    
}
