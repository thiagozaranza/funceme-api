<?php

namespace Funceme\RestfullApi\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Funceme\RestfullApi\Http\Requests\RestHttpRequest;
use Funceme\RestfullApi\DTOs\DataObjectDTO;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $service;
    protected $model_class;

    public function __construct()
    {
        if (isCustomRoute(request()->url())) {
            $this->service = serviceFactory($this);
            $this->model_class = $this->service->repository->getModelClass();
        }
    }

    /**
     * Lists a paginated collection in json format
     * 
     * @param App\Http\Requests\RestHttpRequest $request
     * 
     * @throws Exception
     * @throws Illuminate\Database\QueryException
     * @throws Illuminate\Auth\Access\AuthorizationException
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function index(RestHttpRequest $request)
    {
        try {

            $meta_request = $request->parse($this, __FUNCTION__);
            
            if ($meta_request->isPersonalToken())
                $this->authorize(__FUNCTION__, $this->model_class);

            $response = $this->service
                ->setMetaRequest($meta_request)
                ->get()
                ->toArray();

            return response()->json($response, 200);

        } catch (AuthorizationException $e) {
            return response()->json(array('message' => $e->getMessage()), 401);
        } catch (QueryException $e) {
            return response()->json(array('message' => $e->getMessage()), 403);
        } catch (Exception $e) {
            return response()->json(array('message' => $e->getMessage()), 500);
        }
    }

    /**
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id, RestHttpRequest $request)
    {
        try {
            $object = call_user_func($this->model_class .'::findOrFail', $id);

            $meta_request = $request->parse($this, __FUNCTION__, $id);
            
            if ($meta_request->isPersonalToken())
                $this->authorize(__FUNCTION__, $object);

            $response = $this->service
                ->setMetaRequest($meta_request)
                ->get()
                ->toArray();

            return response()->json($response, 200);

        } catch (AuthorizationException $e) {
            return response()->json(array('message' => $e->getMessage()), 401);
        } catch (QueryException $e) {
            return response()->json(array('message' => $e->getMessage()), 403);
        } catch (ModelNotFoundException $e) {
            return response()->json(array('message' => $e->getMessage()), 404);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function store(RestHttpRequest $request)
    {
        try {
            $meta_request = $request->parse($this, __FUNCTION__);

            if ($meta_request->isPersonalToken())
                $this->authorize(__FUNCTION__, $meta_request->getModel());

            $response = $this->service
                ->setMetaRequest($meta_request)
                ->store();

            return response()->json(array('data' => $response->toArray()), 201);
        } catch (AuthorizationException $e) {
            return response()->json(array('message' => $e->getMessage()), 401);
        } catch (QueryException $e) {
            return response()->json(array('message' => $e->getMessage()), 403);
        } catch (Exception $e) {
            return response()->json(array('message' => $e->getMessage()), 500);
        }
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function update($id, RestHttpRequest $request)
    {
        try {
            $object = call_user_func($this->model_class .'::findOrFail', $id);

            $meta_request = $request->parse($this, __FUNCTION__, $id);

            if ($meta_request->isPersonalToken())
                $this->authorize(__FUNCTION__, $object);

            $response = $this->service
                ->setMetaRequest($meta_request)
                ->update();

            return response()->json(array('data' => $response->toArray()), 200);

        } catch (AuthorizationException $e) {
            return response()->json(array('message' => $e->getMessage()), 401);
        } catch (QueryException $e) {
            return response()->json(array('message' => $e->getMessage()), 403);
        } catch (ModelNotFoundException $e) {
            return response()->json(array('message' => $e->getMessage()), 404);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }        
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function destroy($id, RestHttpRequest $request)
    {
        try {
            $object = call_user_func($this->model_class .'::findOrFail', $id);

            $meta_request = $request->parse($this, __FUNCTION__, $id);

            if ($meta_request->isPersonalToken())
                $this->authorize(__FUNCTION__, $object);

            $this->service
                ->setMetaRequest($request->parse($this, __FUNCTION__, $id))
                ->get();

            return response()->json(array('message' => 'Object deleted.'), 200);

        } catch (AuthorizationException $e) {
            return response()->json(array('message' => $e->getMessage()), 401);
        } catch (QueryException $e) {
            return response()->json(array('message' => $e->getMessage()), 403);
        } catch (ModelNotFoundException $e) {
            return response()->json(array('message' => $e->getMessage()), 404);
        } catch (Exception $e) {
            return response()->json(array('message' => $e->getMessage()), 500);
        }  
    }

    /**
    * For
    *
    **/
    public function call_rpc(RestHttpRequest $request)
    {
        try {

            $meta_request = $request->parse($this, __FUNCTION__);

            if ($meta_request->isPersonalToken() && !Auth::user()->can("Run " . get_class($this->service))) 
                throw new AuthorizationException('This action is unauthorized.');

            $object = $this->service
                ->setMetaRequest($meta_request)
                ->get();

            return response()->json($object->toArray(), 200);
        } catch (AuthorizationException $e) {
            return response()->json(array('message' => $e->getMessage()), 401);
        } catch (QueryException $e) {
            return response()->json(array('message' => $e->getMessage()), 403);
        } catch (ModelNotFoundException $e) {
            return response()->json(array('message' => $e->getMessage()), 404);
        } catch (Exception $e) {
            return response()->json(array('message' => $e->getMessage()), 500);
        }  
    }
}
