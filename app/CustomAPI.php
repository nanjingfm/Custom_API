<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomAPI extends Model
{
    //
    protected $table = 'customerapi';
    private $error = '';
    
    public function getError()
    {
        return $this->error;
    }
    
    public function store($data)
    {
        if (empty($data['username']) || empty($data['name']) || empty($data['path']) || empty($data['response'])) {
            $this->error = '参数错误:' . json_encode($data);
        }
        if (!preg_match('/[^,:{}\\[\\]0-9.\-+Eaeflnr-u \n\r\t]/',$data['response'])) {
            $this->error = '返回值不是有效的json';
            return false;
        }
        
        $api = $this;
        if (!empty($data['id'])) {
            $api = CustomAPI::where('id', $data['id'])->first() ?: $this;
        }
        
        $api->username = $data['username'];
        $api->name = $data['name'];
        $api->path = $data['path'];
        $api->response = $data['response'];
        return $api->save();
    }
    
    public static function handleApiRequest($request)
    {
        $path = $request->getRequestUri();
        $api = self::where('path', $path)->first();
        if ($api) {
            $response_data = json_decode($api->response, true);
            $input_data = file_get_contents('php://input', 'r');
            try {
                $input_data = json_decode($input_data);
            } catch (Exception $e) {
            }
            $response_data = array_merge($response_data, [
                'request_data' => $input_data
            ]);            
            return response()->json($response_data);
        }
        return false;
    }
}
