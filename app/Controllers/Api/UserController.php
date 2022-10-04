<?php

namespace App\Controllers\Api;


use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use \App\Validation\CustomRules;


class UserController extends ResourceController

{

public function register()
    {
        $rules = [
            "user_name" => "required",
            "user_email" => "required|valid_email|is_unique[users.user_email]|min_length[5]",
            "user_role" => "required",
            "user_password" => "required",
        ];

        $messages = [
            "user_name" => [
                "required" => "Name is required"
            ],
            "user_email" => [
                "required" => "Email required",
                "valid_email" => "Email address is not in format"
            ],
            "user_role" => [
                "required" => "user role is required",
               
            ],
            "user_password" => [
                "required" => "password is required"
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

            $userModel = new UserModel();

            $data = [
                "user_name" => $this->request->getVar("user_name"),
                "user_email" => $this->request->getVar("user_email"),
                "user_role" => $this->request->getVar("user_role"),
                "user_password" => password_hash($this->request->getVar("user_password"), PASSWORD_DEFAULT),
            ];

            if ($userModel->insert($data)) {

                $response = [
                    'status' => 200,
                    "error" => false,
                    'message' => 'Successfully, user has been registered',
                    'data' => []
                ];
            } else {

                $response = [
                    'status' => 500,
                    "error" => true,
                    'message' => 'Failed to create user',
                    'data' => []
                ];
            }
        }

        return $this->respond($response);
    }

    public function login()
    {
        $rules = [
            "user_email" => "required|valid_email|min_length[5]",
            "user_password" => "required",
        ];

        $messages = [
            "user_email" => [
                "required" => "Email required",
                "valid_email" => "Email address is not in format"
            ],
            "user_password" => [
                "required" => "password is required"
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
            $userModel = new UserModel();

            $userdata = $userModel->where("user_email", $this->request->getVar("user_email"))->first();
// first search for the user in the database using the email sent in the post (getVar)
            if (!empty($userdata)) {
// second , if the result of the research is not empty
                if (password_verify($this->request->getVar("user_password"), $userdata['user_password'])) { 
// third , compare the password and do the token generation
                    $key = getenv('JWT_SECRET');

                    $iat = time(); // current timestamp value
                    $nbf = $iat;
                    $exp = $iat + 3600;

                    $payload = array(
                        "iat" => $iat, // issued at
                        "nbf" => $nbf, //not before in seconds
                        "exp" => $exp, // expire time in seconds
                        "data" => array(
                                    'id' => $userdata['id_user'],
                                    'name' => $userdata['user_name'],
                                    'role' => $userdata['user_role'],
                                ),
                    );
                   
                   $token = JWT::encode($payload, $key, 'HS256');
                    $response = [
                        'status' => 200,
                        'error' => false,
                        'message' => 'User logged In successfully',
                        'data' => [
                            'token' => $token
                        ]
                    ];
                } else {
// if the passwords do not match
                    $response = [
                        'status' => 500,
                        'error' => true,
                        'message' => 'Incorrect details',
                        'data' => []
                    ];
                }
            } else {  // if the user is not found in the database
                $response = [
                    'status' => 500,
                    'error' => true,
                    'message' => 'User not found',
                    'data' => []
                ];
               
            }
        }
        return $this->respond($response);
    }

    public function details()
    {
        $key = getenv('JWT_SECRET');

        try {
            //$header = $this->request->getHeader("Authorization");
            $token = $this->request->getServer("HTTP_AUTHORIZATION");
            $token = str_replace('Bearer ', '', $token);
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            if ($decoded) {
                $response = [
                    'status' => 200,
                    'error' => false,
                    'message' => 'User details',
                    'data' => [
                        'profile' => $decoded->data
                    ]
                ];
            }
        } catch (Exception $ex) {
            $response = [
                'status' => 401,
                'error' => true,
                'message' => 'Access denied',
                'data' => []
            ];
           
        }
        return $this->respond($response);
    }






    public function changeMail()
    {
        $rules = [
            "old_email" => "required|valid_email|min_length[5]",
            "user_email" => "required|valid_email|is_unique[users.user_email]|min_length[5]",
            "user_password" => "required",
        ];

        $messages = [
            "old_email" => [
                "required" => "Email required",
                "valid_email" => "Email address is not in format"
            ],
            "user_email" => [
                "required" => "Email required",
                "valid_email" => "Email address is not in format"
            ],
            "user_password" => [
                "required" => "password is required"
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
            $userModel = new UserModel();

            $userdata = $userModel->where("user_email", $this->request->getVar("old_email"))->first();
// first search for the user in the database using the email sent in the post (getVar)
            if (!empty($userdata)) {
// second , if the result of the research is not empty
                if (password_verify($this->request->getVar("user_password"), $userdata['user_password'])) { 
// third , compare the password 
                    


            $data['user_email'] = $this->request->getVar("user_email");
            $userModel->update($userdata['id_user'], $data);  


                    $response = [
                        'status' => 200,
                        'error' => false,
                        'message' => 'Mail chaged successfully',
                        'data' => []
                    ];
                } else {
// if the passwords do not match
                    $response = [
                        'status' => 500,
                        'error' => true,
                        'message' => 'Incorrect details',
                        'data' => []
                    ];
                }
            } else {  // if the user is not found in the database
                $response = [
                    'status' => 500,
                    'error' => true,
                    'message' => 'User not found',
                    'data' => []
                ];
               
            }
        }
        return $this->respond($response);
    }


    public function changePass()
    {
        $rules = [
            "old_password" => "required",
            "user_email" => "required|valid_email|min_length[5]",
            "user_password" => "required",
        ];

        $messages = [
            "old_password" => [
                "required" => " password required",
                
            ],
            "user_email" => [
                "required" => "Email required",
                "valid_email" => "Email address is not in format"
            ],
            "user_password" => [
                "required" => "New password is required"
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
            $userModel = new UserModel();

            $userdata = $userModel->where("user_email", $this->request->getVar("user_email"))->first();
// first search for the user in the database using the email sent in the post (getVar)
            if (!empty($userdata)) {
// second , if the result of the research is not empty
                if (password_verify($this->request->getVar("old_password"), $userdata['user_password'])) { 
// third , compare the password 
                    


            $data['user_password'] = password_hash($this->request->getVar("user_password"), PASSWORD_DEFAULT);
            $userModel->update($userdata['id_user'], $data);  


                    $response = [
                        'status' => 200,
                        'error' => false,
                        'message' => 'Password changed successfully',
                        'data' => []
                    ];
                } else {
// if the passwords do not match
                    $response = [
                        'status' => 500,
                        'error' => true,
                        'message' => 'Incorrect details',
                        'data' => []
                    ];
                }
            } else {  // if the user is not found in the database
                $response = [
                    'status' => 500,
                    'error' => true,
                    'message' => 'User not found',
                    'data' => []
                ];
               
            }
        }
        return $this->respond($response);
    }



    public function changeRole()
    {
        $rules = [
            "id_user" => "required",
            "user_role" => "required",
        ];

        $messages = [
            "id_user" => [
                "required" => " user id is required",
                
            ],

            "user_role" => [
                "required" => "user Role is required"
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
            $userModel = new UserModel();

            $userdata = $userModel->where("id_user", $this->request->getVar("id_user"))->first();
// first search for the user in the database using the id_user sent in the post (getVar)
            if (!empty($userdata)) {
// second , if the result of the research is not empty

                $data['user_role'] =$this->request->getVar("user_role");
                $userModel->update($userdata['id_user'], $data);  


              
                        $response = [
                            'status' => 200,
                            'error' => false,
                            'message' => 'user Role changed successfully',
                            'data' => []
                        ];
                    
            } else {  // if the user is not found in the database
                        $response = [
                            'status' => 500,
                            'error' => true,
                            'message' => 'User not found',
                            'data' => []
                        ];
                    
            }
        }
        return $this->respond($response);
    }



	public function listUser()
	{
        $userModel = new UserModel();
            
       
        
		$response = [
			'status' => 200,
			"error" => false,
			'messages' => 'User list',
			'data' => $userModel->findAll()
		];

		return $this->respond($response);
	}

    public function showUser ($user_id)
	{
		$userModel = new UserModel();

		$data = $userModel->find($user_id);
        //$data = $model->where(['id' => $user_id])->first();

		if (!empty($data)) {

			$response = [
				'status' => 200,
				"error" => false,
				'messages' => 'single user data',
				'data' => $data
			];

		} else {

			$response = [
				'status' => 500,
				"error" => true,
				'messages' => 'No user found',
				'data' => []
			];
		}

		return $this->respond($response);
	}






}


   

