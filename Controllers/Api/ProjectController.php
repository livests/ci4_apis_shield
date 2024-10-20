<?php

namespace App\Controllers\Api;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ProjectController extends ResourceController
{
    protected $modelName = "App\Models\ProjectModel";
    protected $format = "json";

    //add project [post] -> user_id, project name, project_budget, description
    public function addProject(){
        $validationRules = [
            "project_name" => "required",
            "project_budget" => "required",
            "description" => "required",
        ];
        if(!$this->validate($validationRules)){
            return $this->respond([
                "status" => false,
                "message" => "Project inputs are required",
            ]);
        }
        $userId = auth()->user()->id;
        if($this->model->insert([
            "user_id" => $userId,
            "project_name" => $this->request->getPost("project_name"),
            "project_budget" => $this->request->getPost("project_budget"),
            "description" => $this->request->getPost("description"),
        ])){
            return $this->respond([
                "status" => true,
                "message" => "Project added successfully",
            ]);
        }else{
            return $this->respond([
                            "status" => false,
                            "message" => "Failed to add project for this user",
                        ]);
        
        
        }
    }

    //list all projects user wise 
    public function getProjects(){
        $userId =  auth()->user()->id;
        $projects = $this->model->where("user_id", $userId)->findAll();
        if($projects){
            return $this->respond([
                            "status" => true,
                            "message" => "Projects fetched successfully",
                            "data" => $projects,
                        ]);
        }else{
            return $this->respond([
                            "status" => false,
                            "message" => "No projects found for this user",
                        ]);
        }

    }
}
