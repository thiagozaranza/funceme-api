<?php

namespace Funceme\RestfullApi\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Funceme\RestfullApi\Http\Requests\RestHttpRequest;
use Funceme\RestfullApi\DTOs\DataObjectDTO;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $service;
    protected $model_class;
    private $status_code = 200;

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
            
            //if ($meta_request->isPersonalToken())
            //    $this->authorize(__FUNCTION__, $this->model_class);

            $response = $this->service
                ->setMetaRequest($meta_request)
                ->get()
                ->toArray();

        } catch (AuthorizationException $e) {
            $this->status_code = 401;
            $response = [
                'message'   => $e->getMessage(),
                'trace'     => $e->getTrace()
            ];
        } catch (QueryException $e) {
            $this->status_code = 500;
            $response = [
                'message'   => $e->getMessage(),
                'sql'       => $e->getSql()
            ];
        } catch (Exception $e) {
            $this->status_code = 500;
            $response = [
                'message'   => $e->getMessage(),
                'trace'     => $e->getTrace()
            ];
        }

        return response()->json($response, $this->status_code);
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
            
            //if ($meta_request->isPersonalToken())
            //    $this->authorize(__FUNCTION__, $object);

            $response = $this->service
                ->setMetaRequest($meta_request)
                ->get()
                ->toArray();

        } catch (AuthorizationException $e) {
            $this->status_code = 401;
            $response = [
                'message'   => $e->getMessage()
            ];
        } catch (ModelNotFoundException $e) {
            $this->status_code = 404;
            $response = [
                'message'   => 'Objeto não encontrado.'
            ];
        } catch (Exception $e) {
            $this->status_code = 500;
            $response = [
                'message'   => $e->getMessage(),
                'trace'     => $e->getTrace()
            ];
        }

        return response()->json($response, $this->status_code);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function store(RestHttpRequest $request)
    {
        try {
            $meta_request = $request->parse($this, __FUNCTION__);

            $response = $this->service
                ->setMetaRequest($meta_request)
                ->store();

            return response()->json(array('data' => $response->toArray()), 201);
        } catch (Exception $e) {
            return response()->json(array('message' => $e->getMessage()), 417);
        } catch (QueryException $qe) {
            return response()->json(array('message' => $qe->getMessage()), 417);
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
            $meta_request = $request->parse($this, __FUNCTION__, $id);

            $response = $this->service
                ->setMetaRequest($meta_request)
                ->update();

        } catch (Exception $e) {
            return response()->json(array('message' => $e->getMessage(), 'trace' => $e->getTrace()), 417);
        }

        return response()->json(array('data' => $response->toArray()), 200);
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
            $object = $this->service
                ->setMetaRequest($request->parse($this, __FUNCTION__, $id))
                ->get();

        } catch (Exception $e) {
            return response()->json(array('success' => false, 'message' => $e->getMessage()), 417);
        }

        return response()->json(array('success' => true));
    }

    /**
    * For
    *
    **/
    public function call_rpc(RestHttpRequest $request)
    {
        $object = $this->service
            ->setMetaRequest($request->parse($this, __FUNCTION__))
            ->get();

        return response()->json($object->toArray(), 200);
    }
}
