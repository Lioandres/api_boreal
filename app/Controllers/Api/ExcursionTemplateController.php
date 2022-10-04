<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ExcursionTemplateModel;

class ExcursionTemplateController extends ResourceController
{
    public function addExcursionTemplate()
	{
		$rules = [
			"title" => "required|is_unique[excursions_template.title]",
			"img" => "required",
			"type" => "required",
			"description" => "required",
			"price_default" => "required",
			"max_num_people_default" => "required",
		];

		$messages = [
			"title" => [
				"required" => "title is required",
				"is_unique" => "title already exists"
			],

			"img" => [
				"required" => "img route is required",	
			],

			"type" => [
				"required" => "type is required"
			],

			"description" => [
				"required" => "description is required"
			],

			"price_default" => [
				"required" => "price is required"
			],

			"max_num_people_default" => [
				"required" => "max number of people is required"
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

			$emp = new ExcursionTemplateModel();

			$data['title'] = $this->request->getVar("title");
			$data['img'] = $this->request->getVar("img");
			$data['type'] = $this->request->getVar("type");
			$data['description'] = $this->request->getVar("description");
			$data['price_default'] = $this->request->getVar("price_default");
			$data['max_num_people_default'] = $this->request->getVar("max_num_people_default");

			$emp->save($data);

			$response = [
				'status' => 200,
				'error' => false,
				'message' => 'Excursion Template added successfully',
				'data' => []
			];
		}

		return $this->respond($response);
	}

	public function listExcursionTemplate()
	{
		$emp = new ExcursionTemplateModel();
            
        //log_message('error', $e->getMessage());
        
		$response = [
			'status' => 200,
			"error" => false,
			'messages' => 'Excursion list',
			'data' => $emp->findAll()
		];

		return $this->respond($response);
	}

	public function showExcursionTemplate ($emp_id)
	{
		$emp = new ExcursionTemplateModel();

		$data = $emp->find($emp_id);
        //$data = $model->where(['id' => $emp_id])->first();

		if (!empty($data)) {

			$response = [
				'status' => 200,
				"error" => false,
				'messages' => 'Single excursion template data',
				'data' => $data
			];

		} else {

			$response = [
				'status' => 500,
				"error" => true,
				'messages' => 'No excursion template found',
				'data' => []
			];
		}

		return $this->respond($response);
	}

	public function updateExcursionTemplate ($emp_id)
	{
		$rules = [
			"title" => "required",
			"img" => "required",
			"type" => "required",
			"description" => "required",
			"price_default" => "required",
			"max_num_people_default" => "required",
		];

		$messages = [
			"title" => [
				"required" => "title is required",
				
			],

			"img" => [
				"required" => "img route is required",	
			],

			"type" => [
				"required" => "type is required"
			],

			"description" => [
				"required" => "description is required"
			],

			"price_default" => [
				"required" => "price is required"
			],

			"max_num_people_default" => [
				"required" => "max number of people is required"
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

			$emp = new ExcursionTemplateModel();

			if ($emp->find($emp_id)) {

				$data['title'] = $this->request->getVar("title");
				$data['img'] = $this->request->getVar("img");
				$data['type'] = $this->request->getVar("type");
				$data['description'] = $this->request->getVar("description");
				$data['price_default'] = $this->request->getVar("price_default");
				$data['max_num_people_default'] = $this->request->getVar("max_num_people_default");

				$emp->update($emp_id, $data);

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

	public function deleteExcursionTemplate($emp_id)
	{
		$emp = new ExcursionTemplateModel();

		$data = $emp->find($emp_id);

		if (!empty($data)) {

			$emp->delete($emp_id);

			$response = [
				'status' => 200,
				"error" => false,
				'message' => 'Excursion template deleted successfully',
				'data' => []
			];

		} else {

			$response = [
				'status' => 500,
				"error" => true,
				'message' => 'No excursion template found',
				'data' => []
			];
		}

		return $this->respond($response);
	}
}
