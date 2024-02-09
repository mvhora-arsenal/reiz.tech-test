<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobRequest;
use App\Services\JobService;

class JobController extends Controller
{
    public JobService $jobService;

    public function __construct(JobService $jobsService)
    {
        $this->jobService = $jobsService;
    }

    /**
     * Register and Fetch All the Available Jobs in Redis
     */
    public function create(JobRequest $request)
    {
        $requestData = $request->all();

        $result = $this->jobService->register($requestData);

        $responseData = [
            'status' => 200,
            'message' => "Url's has been saved.",
            'url_id' => $result
        ];
        return response()->json(['data' => $responseData]);
    }

   /**
     * Get Job by ID From Redis
     */
    public function getJobDetails($id)
    {
        $getJob = $this->jobService->getJobById($id);
        return response()->json([$id => $getJob]);
    }

    /**
     * Get All Job From Redis
     */
    public function getAllJobs()
    {
        $getJobs = $this->jobService->getAllJob();
        return response()->json(['data' => $getJobs]);
    }

    /**
     * Delete Job From Redis By ID
     */
    public function deleteJob($id)
    {
        $getJobData = $this->jobService->deleteJobById($id);
        return response()->json(['response' => $getJobData]);
    }
}
