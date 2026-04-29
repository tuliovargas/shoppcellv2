<?php

namespace App\Http\Controllers;

use App\datasources\Datasource;
use App\Models\Client;
use App\Services\Client\DataTableClientService;
use App\Services\Client\IndexClientService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * @var IndexClientService
     */
    private $indexClientService;

    /**
     * ClientController constructor.
     * @param IndexClientService $indexClientService
     */
    public function __construct(IndexClientService $indexClientService)
    {
        //$this->middleware('permission: register client')->except('index');
        $this->indexClientService = $indexClientService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getClientsDatasource(Request $request)
    {
        $datasource = $request->input('datasource');

        $clients = Datasource::getInstance()
            ->setModel(Client::class)
            ->setDatasourceConfig($datasource)
            ->get();

        return response()->json(['data' => $clients]);
    }

    /**
     * @param Request $request
     * @param DataTableClientService $dataTableClientService
     * @return Application|Factory|View
     */
    public function index(Request $request, DataTableClientService $dataTableClientService)
    {
        if ($request->ajax() && $request->get('type') !== 'vue') {
            return $dataTableClientService->run();
        }

        $search = null;
        if ($request->search) {
            $search = $request->search;
        }
        $clients = $this->indexClientService->run($request);

        if ($request->expectsJson() && $request->get('type') === 'vue') {
            return response($clients);
        }

        return view('clients.index')
            ->with('clients', $clients)
            ->with('search', $search);
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|RedirectResponse|Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'cpf' => [
                function ($attribute, $value, $fail) {
                    $cpf = preg_replace('/[^0-9]/', '', $value);
                    $findClient = Client::query()
                        ->where('cpf', '=', $cpf)
                        ->first();
                    if (!empty($findClient)) {
                        $fail('Já existe um cliente cadastrado com esse CPF/CNPJ');
                    }
                }
            ]
        ]);
        $data = $request->all();

        $user = auth()->user();


        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $fileName = time() . '.' . $photo->getClientOriginalExtension();
            $data['photo_url'] = $photo->storeAs('public/clients_photos', $fileName, 'local');
            $data['photo_url'] = str_replace('public/', '', $data['photo_url']);
        }

        $data['postcode'] = isset($data['postcode']) ? preg_replace('/[^0-9]/', '', $data['postcode']) : null;
        $data['cpf'] = isset($data['cpf']) ? preg_replace('/[^0-9]/', '', $data['cpf']) : null;
        $data['rg'] = isset($data['rg']) ? preg_replace('/[^0-9]/', '', $data['rg']) : null;
        $data['cellphone'] = isset($data['cellphone']) ? preg_replace('/[^0-9]/', '', $data['cellphone']) : null;
        $data['phone'] = isset($data['phone']) ? preg_replace('/[^0-9]/', '', $data['phone']) : null;
        $data['is_active'] = isset($data['is_active']) ? true : false;
        $client = new Client($data);

        $client->user_add_id = $user->id;
        $client->save();

        $client->address()->create($data);

        $client->load('address');

        if ($request->expectsJson()) {
            return response($client);
        }

        return redirect()->route('clients.index')->with([
            'success' => true,
            'message' => 'Cliente cadastrado com sucesso'
        ]);
    }

    /**
     * @return Application|Factory|View
     */
    public function show(Request $request, Client $client)
    {
        $client->address;
        return response()->json($client);
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $client = Client::findOrFail($id);

        return view('clients.edit', compact('client'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|ResponseFactory|RedirectResponse|Response
     */
    public function update(Request $request, $id)
    {
        /* Validando se o CPF/CPNJ já não existe antes de atualizar */
        $request->validate([
            'cpf' => [
                function ($attribute, $value, $fail) use ($id) {
                    $cpf = preg_replace('/[^0-9]/', '', $value);
                    $findClient = Client::query()
                        ->where('cpf', '=', $cpf)
                        ->where('id', '!=', $id)
                        ->first();
                    if (!empty($findClient)) {
                        $fail('Já existe um cliente cadastrado com esse CPF/CNPJ');
                    }
                }
            ]
        ]);
        $data = $request->all();
        $client = Client::findOrFail($id);

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $fileName = time() . '.' . $photo->getClientOriginalExtension();
            $data['photo_url'] = $photo->storeAs('public/clients_photos', $fileName, 'local');
            $data['photo_url'] = str_replace('public/', '', $data['photo_url']);
        }

        $data['postcode'] = isset($data['postcode']) ? preg_replace('/[^0-9]/', '', $data['postcode']) : null;
        $data['cpf'] = isset($data['cpf']) && !empty($data['cpf']) ? preg_replace('/[^0-9]/', '', $data['cpf']) : null;
        $data['rg'] = isset($data['rg']) ? preg_replace('/[^0-9]/', '', $data['rg']) : null;
        $data['cellphone'] = isset($data['cellphone']) ? preg_replace('/[^0-9]/', '', $data['cellphone']) : null;
        $data['phone'] = isset($data['phone']) ? preg_replace('/[^0-9]/', '', $data['phone']) : null;
        $data['is_active'] = $data['is_active'] ? true : false;

        $client->update($data);

        $address = [
            'postcode' => isset($data['postcode']) ? $data['postcode'] : null,
            'street' => isset($data['street']) ? $data['street'] : null,
            'neighborhood' => isset($data['neighborhood']) ? $data['neighborhood'] : null,
            'number' => isset($data['number']) ? $data['number'] : null,
            'city' => isset($data['city']) ? $data['city'] : null,
            'state' => isset($data['state']) ? $data['state'] : null,
        ];

        $client->address()->update($address);

        $client->load('address');

        if ($request->expectsJson()) {
            return response($client);
        }

        return redirect()->route('clients.index')->with([
            'success' => true,
            'message' => 'Cliente editado com sucesso'
        ]);
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function delete($id)
    {

        $client = Client::findOrFail($id);

        return view('clients.delete', compact('client'));
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        Client::destroy($id);

        return redirect()->route('clients.index')->with([
            'success' => true,
            'message' => 'Cliente excluído com sucesso'
        ]);
    }

    public function getCount($when = null)
    {
        $query = Client::query();
        if ($when) {
            $query->where(DB::raw('DATE(`created_at`)'), '=', DB::raw('DATE("' . $when . '")'));
        }
        return response([
            'count' => $query->get()->count()
        ]);
    }

    /**
     * 
     *
     * @param Request $request
     * @return boolean Retorna `false` quando o CPF/CNPJ já existir
     */
    public function validateCpfCnpj(Request $request)
    {
        return response()->json(
            empty(Client::query()
                ->where('cpf', '=', $request->cpfCnpj)
                ->first())
        );
    }
}
