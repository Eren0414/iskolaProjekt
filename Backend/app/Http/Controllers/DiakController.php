<?php

namespace App\Http\Controllers;

use App\Models\Diak;
use App\Http\Requests\StoreDiakRequest;
use App\Http\Requests\UpdateDiakRequest;
use Illuminate\Support\Facades\DB;

class DiakController extends Controller
{
    public function index()
    {
        $rows = Diak::with('osztalies')->get(); // Betölti az osztály adatokat is
        $sql = "SELECT d.id, d.osztalyId, o.osztalyNev, d.nev, d.neme, d.szuletett, d.helyseg, d.osztondij, d.atlag from diaks d
            INNER JOIN osztalies o ON d.osztalyId = o.id";

        $rows = DB::select($sql);
        $data = [
            'message' => 'ok',
            'data' => $rows,
        ];
        return response()->json($data, options: JSON_UNESCAPED_UNICODE);
    }

    public function store(StoreDiakRequest $request)
    {
        try {
            $row = Diak::create($request->all());
            $data = [
                'message' => 'ok',
                'data' => $row
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            $data = [
                'message' => 'The post failed',
                'data' => $request->all()
            ];
        }

        return response()->json($data, options: JSON_UNESCAPED_UNICODE);
    }

    public function show(int $id)
    {
        $row = Diak::find($id);
        if ($row) {
            $data = [
                'message' => 'ok',
                'data' => $row
            ];
        } else {
            $data = [
                'message' => 'Not found',
                'data' => [
                    'id' => $id
                ]
            ];
        }
        return response()->json($data, options: JSON_UNESCAPED_UNICODE);
    }

    public function update(UpdateDiakRequest $request,  $id)
    {
        $row = Diak::find($id);
        if ($row) {

            try {
                $row->update($request->all());
                $data = [
                    'message' => 'ok',
                    'data' => $row
                ];
            } catch (\Illuminate\Database\QueryException $e) {
                $data = [
                    'message' => 'The patch failed',
                    'data' => $request->all()
                ];
            }
        } else {
            $data = [
                'message' => 'Not found',
                'data' => [
                    'id' => $id
                ]
            ];
        }
        return response()->json($data, options: JSON_UNESCAPED_UNICODE);
    }

    public function destroy(int $id)
    {
        $row = Diak::find($id);
        if ($row) {
            $row->delete();
            $data = [
                'message' => 'ok',
                'data' => [
                    'id' => $id
                ]
            ];
        } else {
            $data = [
                'message' => 'Not found',
                'data' => [
                    'id' => $id
                ]
            ];
        }
        return response()->json($data, options: JSON_UNESCAPED_UNICODE);
    }
}
