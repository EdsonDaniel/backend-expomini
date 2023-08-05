<?php
namespace App\Http\Controllers;
use App\Models\Articulos;
use App\Models\GrupoPedido;
use App\Models\Pedidos;
use App\Models\PedidoMks;
use App\Models\Cliente;
use App\Models\Usuario;
use App\Models\DetallePedido;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ArticulosController extends Controller
{
    public function index(Request $request){
        $art = Articulos::where('art', '1')->get();
        return response()->json($art);
    }

    public function search_ant(Request $request){
        $lineas = array('LYCH', 'HOGA', 'ALIM', 'MASC', 'BOTA', 'BEBA', 'HYCP', 'HYCP' );
        $buscar = $request->query('search', '');

        if (in_array($buscar, $lineas)) {
            $result  = DB::table('invart')
            ->join('inviar', 'invart.art', '=', 'inviar.art')
            ->leftJoin('invmca', 'inviar.marca', '=', 'invmca.marca')
            
            ->leftJoin('codbar', 
                DB::raw('(codbar.art'), 
                '=', DB::raw('invart.art and cant_pre3 = codbar.factor_uds)'))

            ->leftJoin(
                DB::raw('([TCADBMAB].[dbo].prmdet inner join [TCADBMAB].[dbo].prmhdr 
                on prmhdr.NumProm = prmdet.NumProm)'),
                'invart.art', '=', DB::raw(" prmdet.cve_art and prmhdr.fec_fin >= GETDATE() 
                AND prmhdr.fec_ini <= GETDATE() and TpoProm = 1 ".
                " and prmdet.alm = '001' and prmdet.suc = '001' "
                //"and seg_0 = 'EXP'  and prmhdr.status=1"
                ))
            
            ->where('invart.alm', '001')
            //->where('invart.alm', '099')
            ->where('invart.status', '00')
            ->where('inviar.lin', '=' , $buscar)
            //->where('des1', 'like', '%'.$buscar.'%')
            ->whereNotNull('TpoProm')
            ->select('invart.art','cve_pro','des1','inviar.lin','inviar.s_fam',
            'inviar.s_lin','uds_min','cant_pre3', 'TpoProm', 'prmhdr.NumProm as num_prom',
            'uds as ud_pre3', 'invmca.descr as marca','invart.precio_vta0',
            'invart.precio_vta1','invart.precio_vta2','invart.precio_vta3',
            'invart.precio_vta4',
            'cant_pre0','cant_pre1','cant_pre2','cant_pre3','cant_pre4',
            'cant_vol0', 'cant_vol1', 'cant_vol2', 'cant_vol3',
            'prmdet.precio_3 as pr_prm3', 'invart.imp1')
            ->orderByDesc('cve_pro')
            ->distinct()
            ->limit(50)
            ->get();
        }
        else{

        $result = DB::table('invart')
            ->join('inviar', 'invart.art', '=', 'inviar.art')
            ->leftJoin('invmca', 'inviar.marca', '=', 'invmca.marca')
            
            ->leftJoin('codbar', 
                DB::raw('(codbar.art'), 
                '=', DB::raw('invart.art and cant_pre3 = codbar.factor_uds)'))

            ->leftJoin(
                DB::raw('([TCADBMAB].[dbo].prmdet inner join [TCADBMAB].[dbo].prmhdr 
                on prmhdr.NumProm = prmdet.NumProm)'),
                'invart.art', '=', DB::raw(" prmdet.cve_art and prmhdr.fec_fin >= GETDATE() 
                AND prmhdr.fec_ini <= GETDATE() and TpoProm = 1 ".
                " and prmdet.alm = '001' and prmdet.suc = '001' "
                //"and seg_0 = 'EXP'  and prmhdr.status=1"
                ))
            
            ->where('invart.alm', '001')
            //->where('invart.alm', '099')
            ->where('invart.status', '00')
            ->where('des1', 'like', '%'.$buscar.'%')
            ->whereNotNull('TpoProm')
            ->select('invart.art','cve_pro','des1','inviar.lin','inviar.s_fam',
            'inviar.s_lin','uds_min','cant_pre3', 'TpoProm', 'prmhdr.NumProm as num_prom',
            'uds as ud_pre3', 'invmca.descr as marca','invart.precio_vta0',
            'invart.precio_vta1','invart.precio_vta2','invart.precio_vta3',
            'invart.precio_vta4',
            'cant_pre0','cant_pre1','cant_pre2','cant_pre3','cant_pre4',
            'cant_vol0', 'cant_vol1', 'cant_vol2', 'cant_vol3',
            'prmdet.precio_3 as pr_prm3', 'invart.imp1')
            ->orderByDesc('cve_pro')
            ->distinct()
            ->limit(50)
            ->get();
        }
            //->toArray();

        /*foreach($result as $item){
            $tiene_promo = DB::table('prmhdr')
                ->join('prmdet', 'prmhdr.NumProm', '=', 'prmdet.NumProm')
                ->where('cve_art', $item->art)
                ->where('fec_ini', '>=', 'getdate()')
                ->where('fec_fin', '<=', 'getdate()')
                ->get();
            if($tiene_promo->isEmpty())
            $item->promocion = 0;
            else $item->promocion = 1;
        }*/ 
        return response()->json($result);
    }

    public function search(Request $request){
        $lineas = array('LYCH', 'HOGA', 'ALIM', 'MASC', 'BOTA', 'BEBA', 'HYCP', 
        'HYCP','COLGATE', 'MINI');
        $buscar = $request->query('search', '');
        $buscar = strtoupper($buscar);
        $buscar = trim($buscar);

        if($buscar == ""){
            return [];
        }

        if (in_array($buscar, $lineas)) {
            //return $buscar;
            $result  = DB::table('invart')
            ->join('inviar', 'invart.art', '=', 'inviar.art')
            ->leftJoin('invmca', 'inviar.marca', '=', 'invmca.marca')
            
            ->leftJoin('codbar', 
                DB::raw('(codbar.art'), 
                '=', DB::raw('invart.art and cant_pre3 = codbar.factor_uds)'))
                
            /*->leftJoin(
                DB::raw('([TCADBMAB].[dbo].prmdet inner join [TCADBMAB].[dbo].prmhdr 
                on prmhdr.NumProm = prmdet.NumProm)'),
                'invart.art', '=', 'prmdet.cve_art'*/
                
                /*DB::raw(" prmdet.cve_art and prmhdr.fec_fin >= GETDATE() 
                AND prmhdr.fec_ini <= GETDATE() and TpoProm = 1 ".
                " and prmdet.alm = '001' and prmdet.suc = '001' "*/
                //"and seg_0 = 'EXP'  and prmhdr.status=1"))
            ->leftJoin(
                DB::raw('([TCADBMAB].[dbo].prmdet inner join [TCADBMAB].[dbo].prmhdr 
                on prmhdr.NumProm = prmdet.NumProm)'),
                'invart.art', '=', //'prmdet.cve_art'
                DB::raw(" prmdet.cve_art ".
                //" AND prmhdr.fec_fin >= GETDATE() ".
                //" AND prmhdr.fec_ini <= GETDATE() ".
                " AND convert(char(8), getdate(), 112) between fec_ini and fec_fin ".
                " AND TpoProm = 1 ".
                " AND prmdet.alm = '001' ".
                " AND prmdet.suc = '001' " .
                //"and seg_0 = 'EXP' " .
                " AND prmhdr.status=1")
                )
            //Impuestos
            ->leftJoin('comimp',
                DB::raw('(comimp.clave '),'=', 
                DB::raw(" invart.imp1 and comimp.modulo = 'V')"))
            
            ->where('invart.alm', '001')
            //->where('invart.alm', '099')
            ->where('invart.status', '00')
            ->whereRaw("(inviar.lin = ? or rtrim(invmca.descr) like '%".$buscar."%')", [$buscar, $buscar])
            //->where('inviar.lin', '=' , $buscar)
            
            //->whereNotNull('TpoProm')
            /*
                ->select('invart.art','cve_pro','des1','inviar.lin','inviar.s_fam',
                'inviar.s_lin','uds_min','cant_pre3', 'TpoProm', 'prmhdr.NumProm as num_prom',
                //'uds as ud_pre3', 
                'invmca.descr as marca','invart.precio_vta0',
                'invart.precio_vta1','invart.precio_vta2','invart.precio_vta3',
                'invart.precio_vta4',
                'cant_pre0','cant_pre1','cant_pre2','cant_pre3','cant_pre4',
                'cant_vol0', 'cant_vol1', 'cant_vol2', 'cant_vol3',
                'prmdet.precio_3 as pr_prm3', 'invart.imp1')
                ->orderByDesc('cve_pro')
                ->distinct()
                ->limit(50)
                ->get();
            */
            ->select('invart.art','cve_pro','des1','inviar.lin','inviar.s_fam',
            'inviar.s_lin','uds_min','cant_pre3', 'TpoProm', 'prmhdr.NumProm as num_prom',
            'uds as ud_pre3', 'invmca.descr as marca','invart.precio_vta0',
            'invart.precio_vta1','invart.precio_vta2','invart.precio_vta3',
            'invart.precio_vta4',
            'cant_pre0','cant_pre1','cant_pre2','cant_pre3','cant_pre4',
            'cant_vol0', 'cant_vol1', 'cant_vol2', 'cant_vol3',
            'prmdet.precio_3 as pr_prm3', 'invart.imp1 as imp_1',
            'comimp.imp1 as tasaImp1', 'comimp.imp2 as tasaImp2',
            'comimp.imp3 as tasaImp3', 'fac_ent_sal0 as cve_sim')
            //->orderByDesc('cve_pro')
            ->orderBy('des1')
            ->distinct()
            ->limit(50)
            ->get();
            //->toSql();
            return $result;
        }
        else{

        $result = DB::table('invart')
            ->join('inviar', 'invart.art', '=', 'inviar.art')
            ->leftJoin('invmca', 'inviar.marca', '=', 'invmca.marca')
            
            ->leftJoin('codbar', 
                DB::raw('(codbar.art'), 
                '=', DB::raw('invart.art and cant_pre3 = codbar.factor_uds)'))

            ->leftJoin(
                DB::raw('([TCADBMAB].[dbo].prmdet inner join [TCADBMAB].[dbo].prmhdr 
                on prmhdr.NumProm = prmdet.NumProm)'),
                'invart.art', '=', //'prmdet.cve_art'
                DB::raw(" prmdet.cve_art ".
                //" AND prmhdr.fec_fin >= GETDATE() ".
                //" AND prmhdr.fec_ini <= GETDATE() ".
                " AND convert(char(8), getdate(), 112) between fec_ini and fec_fin ".
                " AND TpoProm = 1 ".
                " AND prmdet.alm = '001' ".
                " AND prmdet.suc = '001' " .
                //"and seg_0 = 'EXP' " .
                " AND prmhdr.status=1")
                )

            //Impuestos
            ->leftJoin('comimp',
                DB::raw('(comimp.clave '),'=', 
                DB::raw(" invart.imp1 and comimp.modulo = 'V')"))
            
            ->where('invart.alm', '001')
            //->where('invart.alm', '099')
            ->where('invart.status', '00');
            //->where('des1', 'like', '%'.$buscar.'%');
            //->orWhere('marca', 'like', '%'.$buscar.'%') 
            //->whereNotNull('TpoProm')

            //Si el parametro de busqueda es solo numeros
            if (preg_match("/^\d+$/", $buscar)) {
                $result = $result->where('invart.art', '=', $buscar);
            } else {
                $palabras = explode(" ", $buscar);
                for ($i=0; $i < count($palabras) ; $i++) {
                    ;
                }
                $result = $result->where('des1', 'like', '%'.$buscar.'%');
            }

            $result = $result
            ->select('invart.art','cve_pro','des1','inviar.lin','inviar.s_fam',
            'inviar.s_lin','uds_min','cant_pre3', 'TpoProm', 'prmhdr.NumProm as num_prom',
            'uds as ud_pre3', 'invmca.descr as marca','invart.precio_vta0',
            'invart.precio_vta1','invart.precio_vta2','invart.precio_vta3',
            'invart.precio_vta4',
            'cant_pre0','cant_pre1','cant_pre2','cant_pre3','cant_pre4',
            'cant_vol0', 'cant_vol1', 'cant_vol2', 'cant_vol3',
            'prmdet.precio_3 as pr_prm3', 'invart.imp1 as imp_1',
            'comimp.imp1 as tasaImp1', 'comimp.imp2 as tasaImp2',
            'comimp.imp3 as tasaImp3', 'fac_ent_sal0 as cve_sim')
            //->orderByDesc('cve_pro')
            ->orderBy('des1')
            ->distinct()
            ->limit(50)
            ->get();
        }
            //->toArray();

        /*foreach($result as $item){
            $tiene_promo = DB::table('prmhdr')
                ->join('prmdet', 'prmhdr.NumProm', '=', 'prmdet.NumProm')
                ->where('cve_art', $item->art)
                ->where('fec_ini', '>=', 'getdate()')
                ->where('fec_fin', '<=', 'getdate()')
                ->get();
            if($tiene_promo->isEmpty())
            $item->promocion = 0;
            else $item->promocion = 1;
        }*/ 
        return response()->json($result);
    }

    public function search2(Request $request){
        $buscar = $request->query('search', '');
        $result = DB::table('invart')
            ->join('inviar', 'invart.art', '=', 'inviar.art')
            ->leftJoin('invmca', 'inviar.marca', '=', 'invmca.marca')
            
            ->leftJoin('codbar', 
                DB::raw('(codbar.art'), 
                '=', DB::raw('invart.art and cant_pre3 = codbar.factor_uds)'))

            ->leftJoin(
                DB::raw('([TCADBMAB].[dbo].prmdet inner join [TCADBMAB].[dbo].prmhdr 
                on prmhdr.NumProm = prmdet.NumProm)'),
                'invart.art', '=', DB::raw(" prmdet.cve_art and prmhdr.fec_fin >= GETDATE() 
                AND prmhdr.fec_ini <= GETDATE() and TpoProm = 1 ".
                " and prmdet.alm = '001' and prmdet.suc = '001' ".
                " and prmhdr.status=1"))
            
            ->where('invart.alm', '001')
            //->where('invart.alm', '099')
            ->where('invart.status', '00')
            ->where('des1', 'like', '%'.$buscar.'%')
            ->select('invart.art','cve_pro','des1','inviar.lin','inviar.s_fam',
            'inviar.s_lin','uds_min','cant_pre3', 'TpoProm', 'prmhdr.NumProm as num_prom',
            'uds as ud_pre3', 'invmca.descr as marca','invart.precio_vta0',
            'invart.precio_vta1','invart.precio_vta2','invart.precio_vta3',
            'invart.precio_vta4',
            'cant_pre0','cant_pre1','cant_pre2','cant_pre3','cant_pre4',
            'cant_vol0', 'cant_vol1', 'cant_vol2', 'cant_vol3',
            'prmdet.precio_3 as pr_prm3', 'invart.imp1')
            ->orderByDesc('cve_pro')
            ->distinct()
            ->limit(20)
            ->get();
            //->toArray();

        /*foreach($result as $item){
            $tiene_promo = DB::table('prmhdr')
                ->join('prmdet', 'prmhdr.NumProm', '=', 'prmdet.NumProm')
                ->where('cve_art', $item->art)
                ->where('fec_ini', '>=', 'getdate()')
                ->where('fec_fin', '<=', 'getdate()')
                ->get();
            if($tiene_promo->isEmpty())
            $item->promocion = 0;
            else $item->promocion = 1;
        }*/ 
        return response()->json($result);
    }

    public function promociones(Request $request){
        $current_date_time = Carbon::now('America/Mexico_City');
        //return $current_date_time;
        $art = $request->query('art', '0');

        $promociones = DB::table('prmhdr')
            ->join('prmdet', 'prmhdr.NumProm', '=', 'prmdet.NumProm')
            ->leftJoin('inviar', 'art_reg', '=', 'inviar.art')
            ->whereRaw("convert(char(8), getdate(), 112) between fec_ini and fec_fin ")
            ->where('prmdet.alm', '=', '001')
            ->where('prmdet.suc', '=', '001')
            ->where('cve_art', '=', $art)
            ->where('prmhdr.status', 1)
            ->select('prmhdr.NumProm as numProm', 'DesProm as descripcion', 'prmhdr.status as estado'
                    , 'inc_similares as aplicaSimilares', 'TpoProm as tipoPromocion', 'sin_cargo as sinCargo'
                    , 'precio_0', 'precio_1', 'precio_2', 'precio_3','precio_4', 'cobradas', 'regaladas', 'emp_reg','art_reg', 'des1 as desc_reg')
            ->orderBy('TpoProm')
            ->orderByDesc('f_alt')
            ->get();

        $presentaciones = DB::table('codbar')
            ->where('art', '=', $art)
            ->distinct()
            ->select('uds', 'factor_uds as factorUds')
            ->orderBy('factor_uds')
            ->get();
            //->toSql()
        
        $datos = array('prm' => $promociones , 'pre'=>$presentaciones);
        return response()->json($datos);
    }

    public function generaPedido(Request $request){
        $data = $request->all();
        $numCliente = "000005937"; //Cliente expo por defecto en caso de cualquier error
        //$nombreCliente = "Cliente Expo";
        $nombreCliente = "";
        $direccion = "";

        if($data["0"]["numCliente"] == ""){
            //Buscar cliente Expo de acuerdo a la sucursal
            $cliente_expo = Cliente::where('suc', $data["0"]["suc"])
                                    ->where('nom', 'like', '%EXPO%')
                                    ->first();
            
            if($cliente_expo != null){
                $numCliente = $cliente_expo->cve;
                $nombreCliente = "";
            }
            else{
                $numCliente = "000005937";
            }
        }
        else{
            $numCliente = $data["0"]["numCliente"];
        }

        //return $array = array('id' => -14, 'estado' => "Paso 2", 'total' => "400");
        
        if($data["0"]["nombreCliente"] != ""){
            $nombreCliente = $data["0"]["nombreCliente"];
            $nombreCliente = strtoupper($nombreCliente);
            //$direccion["0"]["direccionCliente"] != "";
        }
        //001VPQ

        //creando el contenedor del pedido
        try{
            $gpo_ped = new GrupoPedido;
            $gpo_ped->save();
        }catch(\Exception $e) {
            $array = array('id' => "-1", 
            'estado' => "Ocurrió un error al grabar el pedido. Paso 1 "." concat".$e,
            'total' => 0);
            return response()->json($array);
        }
        $tienePqt = false;
        $tieneCja = false;

        foreach ($data as $key => $value) {
            if($value["presentacion"] == "PAQ")
                $tienePqt = true;
            else 
                $tieneCja = true;
            
            if($tieneCja && $tienePqt)
                break;
        }
        
        DB::beginTransaction();

        
        try{

            $pedido_expo_cja = new Pedidos;
            $pedido_expo_paq = new Pedidos;
            if($tieneCja){
                $pedido_expo_cja->sucursal = $data["0"]["suc"];
                $pedido_expo_cja->cliente = $numCliente;
                $pedido_expo_cja->depto = "001VTA";
                $pedido_expo_cja->id_grp_ped = $gpo_ped->id;
                $pedido_expo_cja->nombre_cliente = $nombreCliente;
                //$pedido_expo_cja->sub_alm = '099C';
                $pedido_expo_cja->sub_alm = '001C';
                if($data["0"]["direccion"] != ""){
                    $pedido_expo_cja->direccion = strtoupper($data["0"]["direccion"]); 
                    $pedido_expo_cja->telefono = $data["0"]["telefono"];
                    //$pedido_expo_cja->cp = $data["0"]["codigoP"];
                }
                $pedido_expo_cja->save();
            }

            if($tienePqt){
                $pedido_expo_paq->sucursal = $data["0"]["suc"];
                $pedido_expo_paq->cliente = $numCliente;
                $pedido_expo_paq->depto = "001VPQ";
                $pedido_expo_paq->id_grp_ped = $gpo_ped->id;
                $pedido_expo_paq->nombre_cliente = $nombreCliente;
                //$pedido_expo_paq->sub_alm = '099M';
                $pedido_expo_paq->sub_alm = '001M';

                if($data["0"]["direccion"] != ""){
                    $pedido_expo_paq->direccion = strtoupper($data["0"]["direccion"]); 
                    $pedido_expo_paq->telefono = $data["0"]["telefono"];
                    $pedido_expo_paq->cp = $data["0"]["codigoP"];
                }

                $pedido_expo_paq->save();
            }

            $total_caja = 0;
            $total_paq = 0;

            foreach ($data as $key => $value) {
                $detallePedido = new DetallePedido;
                $detallePedido->cve_art = $value["clave"];
                $detallePedido->cantidad = $value["cantidad"];
                $detallePedido->id_prm = $value["idPrm"];
                $detallePedido->presentacion = $value["presentacion"];
                $detallePedido->precio_n_imp = $value["precioNormal"];
                $detallePedido->precio_cobrado_imp = $value["precioCobrar"];
                $detallePedido->tpo_prm = $value["tpoProm"];

                $precioSinImp = $this->calculaPrecioSinImpuestos(
                    $value["precioNormal"],
                    $value["tasaImp1"], $value["tasaImp2"], $value["tasaImp3"]);

                $precioSinImp2 = $this->calculaPrecioSinImpuestos(
                    $value["precioCobrar"],
                    $value["tasaImp1"], $value["tasaImp2"], $value["tasaImp3"]);

                $detallePedido->impuesto = $value["impuesto"];
                $detallePedido->tasa_imp1 = $value["tasaImp1"];
                $detallePedido->tasa_imp2 = $value["tasaImp2"];
                $detallePedido->tasa_imp3 = $value["tasaImp3"];
                $detallePedido->precio_n = $precioSinImp;
                $detallePedido->precio_cobrado = $precioSinImp2;
                
                if($value["presentacion"] == "PAQ"){
                    $detallePedido->id_ped_expo = $pedido_expo_paq->id;
                    $total_paq += $value["precioCobrar"] * $value["cantidad"];
                }
                else{
                    $detallePedido->id_ped_expo = $pedido_expo_cja->id;
                    $total_caja += $value["precioCobrar"] * $value["cantidad"];
                }

                $detallePedido->save();
            }
            
            if($tieneCja){
                $pedido_expo_cja->total_pedido = $total_caja;
                $pedido_expo_cja->save();
            }

            if($tienePqt){
                $pedido_expo_paq->total_pedido = $total_paq;
                $pedido_expo_paq->save();
            }
            DB::commit();
            $idmks = "";

            
            if($tieneCja){ $idmks = $pedido_expo_cja->id;}
            if($tienePqt){ $idmks .= " ".$pedido_expo_paq->id;}

            $idmks = trim($idmks);
            $array = array('id' => $gpo_ped->id, 'estado' => "", 
            'total_pedido' => $total_caja + $total_paq,"carrito" => $data, "idmks" => $idmks);

        }catch(\Exception $e) {
            $array = array('id' => "-1", 
            'estado' => "Ocurrió un error al grabar el pedido ". $e->getMessage(),
            'total' => 0);
            DB::rollBack();
            return response()->json($array);
        }
        


        try{

            if($tieneCja){
                DB::beginTransaction();
                $result = DB::select('EXEC  RCA_Sincroniza_Bitacoras_Ind '.$pedido_expo_cja->id);
                //$array = array('id' => "-1", 'estado' => $result[0]->mensaje, 'total' => 0);
                //return response()->json($array);
                if( $result[0]->mensaje =='OK'){
                    DB::commit();
                    DB::beginTransaction();
                    $result2 = DB::select('EXEC  RCA_Sincroniza_pedidos_ind ?', array($pedido_expo_cja->id));
                    if($result2[0]->mensaje == 'OK'){
                        DB::commit();
                        $idmerksyst = PedidoMks::where('id_gpo_expo', $pedido_expo_cja->id)->first();
                        if(is_null($idmerksyst)){
                            $idmks = "0";
                        }else $idmks = trim($idmerksyst->num);
                    }else{
                        DB::rollBack();
                    }
                }
                else{
                    DB::rollBack();
                }

                /*
                    //     $result2 = DB::select('EXEC  RCA_Sincroniza_pedidos_ind ?', array($pedido_expo_cja->id));
                    //    // $array = array('id' => "-1", 'estado' => $result2[0]->mensaje, 'total' => 0);
                    //     //return response()->json($array);
                    //         if($result2[0]->mensaje == 'OK'){
                    //             DB::commit();
                    //         }else{
                    //             DB::rollBack();
                    //         }
                */
            }

        
        

            if($tienePqt){
                
                /*
                    //$result2 = DB::select('EXEC  RCA_Sincroniza_pedidos_ind ?', array($pedido_expo_paq->id));
                    // $array = array('id' => "-1", 'estado' => $result2[0]->mensaje, 'total' => 0);
                    //return response()->json($array);
                    //     if($result2[0]->mensaje == 'OK'){
                    //         DB::commit();
                    //     }else{
                    //         DB::rollBack();
                    //     }
                */
                DB::beginTransaction();
                $result = DB::select('EXEC  RCA_Sincroniza_Bitacoras_Ind ?', array($pedido_expo_paq->id));
                if( $result[0]->mensaje =='OK'){
                    DB::commit();
                    DB::beginTransaction();
                    $result2 = DB::select('EXEC  RCA_Sincroniza_pedidos_ind ?', array($pedido_expo_paq->id));
                    if($result2[0]->mensaje == 'OK'){
                        DB::commit();
                        $idmerksyst = PedidoMks::where('id_gpo_expo', $pedido_expo_paq->id)->first();
                        if(is_null($idmerksyst)){
                            $idmks = "0";
                        }else $idmks .= " ".trim($idmerksyst->num);
                    }else{
                        DB::rollBack();
                    }
                }
                else{
                    DB::rollBack();
                }
            }
        }catch(\Exception $e) {
            $array = array('id' => "-1", 'estado' => $e->getMessage(),
            'total' => 0);
            return response()->json($array);
        }

        if(!is_null($idmks)){
            $idmks = trim($idmks);
            $array['idmks'] = $idmks;
        }
        return response()->json($array);
    }

    public function buscaCliente(Request $request){
        $numCli = $request->query('num_cliente', '0');
        $tam = strlen($numCli);
        if($tam > 9){
            return $array = array('-1' );
        }
        $relleno = "";
        
        for ($i= $tam; $i < 9; $i++) { 
            $relleno .= '0';
        }

        $relleno .= $numCli; 

        $cliente = Cliente::
            where('cve', $relleno)
            ->select('nom', 'seg_mer', 'suc', 'cve', 'tel1')
            ->get();

        return response()->json($cliente);

    }

    public function buscaPedido(Request $request){
        $idPedido = $request->query('pedido', '0');
        $pedidos = DB::table('pedido_expo')
            ->join('grupo_pedido', 'pedido_expo.id_grp_ped', '=', 'grupo_pedido.id')
            ->whereRaw("grupo_pedido.id = ". 
            "(select distinct id_grp_ped from pedido_expo where pedido_expo.id_grp_ped = ".
            $idPedido .")")
            ->select('pedido_expo.id')
            ->get();
        
        $idped = $pedidos->pluck('id');
        
        //->where('pedido_expo.id', $idPedido); 
        //->whereIn('id_grp_ped', $array)
        /*$pedido = DB::table('pedido_expo')
            ->join('det_ped_expo', 'pedido_expo.id', '=', 'id_ped_expo')
            ->leftJoin('inviar', 'cve_art', '=', 'art')
            ->where('pedido_expo.id', $idPedido)
            ->select('art', 'des1', 
                    'precio_n as precio_vta0_sin_imp', 'precio_n_imp as precio_vta0' ,  
                    'precio_cobrado as precio_vta1_sin_imp', 
                    'precio_cobrado_imp as precio_vta1', 
                    'presentacion as uds_min', 'cantidad as cant_vol0')
            ->get();*/

        $pedido = DB::table('pedido_expo')
            ->join('det_ped_expo', 'pedido_expo.id', '=', 'id_ped_expo')
            ->leftJoin('inviar', 'cve_art', '=', 'art')
            ->leftJoin('ped_expo_mks', 'pedido_expo.id', '=', 'ped_expo_mks.id_gpo_expo')
            ->whereIn('pedido_expo.id', $idped)
            ->select('art', 'des1', 
                    'precio_n as precio_vta0_sin_imp', 'precio_n_imp as precio_vta0' ,  
                    'precio_cobrado as precio_vta1_sin_imp', 
                    'precio_cobrado_imp as precio_vta1', 
                    'presentacion as uds_min', 'cantidad as cant_vol0', 
                    'tpo_prm as tpoProm', 'det_ped_expo.id_prm as num_prom', 
                    'ped_expo_mks.num as marca')
            ->get();

        return response()->json($pedido);

    }

    public function generaPedido2(Request $request){
        $buscar = $request->query('cl', '');
        $data = $request->all();
        $numCliente = "000005937"; //Cliente expo por defecto en caso de cualquier error
        $nombreCliente = "Cliente Expo";

        $cliente_expo = Cliente::where('suc', '0018')
                                ->where('nom', 'like', '%EXPO%')
                                ->first();

        if($cliente_expo == null)
            return "hola";
            
        return response()->json($cliente_expo);
    }

    public function prueba(Request $request){
        $buscar = $request->query('buscar','0');
        if($buscar == "" || $buscar  == "0")
            $buscar = "0,";
        $buscar = substr($buscar, 0, -1);  // abcd        
        $array = explode(",", $buscar);
        
        $pedidos = DB::table('pedido_expo')
                        ->leftJoin('ped_expo_mks', 'pedido_expo.id', '=', 'ped_expo_mks.id_gpo_expo')
                        ->whereIn('id_grp_ped', $array)
                        ->orderByDesc('id')
                        ->get();
        
        return response()->json($pedidos);

    }

    public function registar(Request $request){
        
        $usuario = new Usuario;
    }

    public function consultaPrecios(Request $request){
        $buscar = $request->query('buscar','0');
        if($buscar == "" || $buscar  == "0")
            $buscar = "0,";
        $buscar = substr($buscar, 0, -1);  // abcd        
        $array = explode(",", $buscar);

        $pedidos = DB::table('invart')
                        //->join('grupo_pedido', 'pedido_expo.id', '=', 'grupo_pedido.id')
                        ->whereIn('art', $array)
                        ->select('precio_vta0', 
                        'precio_vta1','precio_vta2','precio_vta3',
                        'precio_vta4')
                        //->orderByDesc('id')
                        ->get();
        
        return response()->json($pedidos);
    }

    public function sinc_manual(Request $request){
        $buscar = $request->query('buscar','');

        if($buscar == ""){
            $array = array('id' =>"-1", 'estado' => "", 'total' => "");
            return response()->json($array);
        }
        

        if($buscar != ""){
            try{
                DB::beginTransaction();
                $result = DB::select('EXEC  RCA_Sincroniza_Bitacoras_Ind ?', array($buscar));
                if( $result[0]->mensaje =='OK'){
                    DB::commit();
                    DB::beginTransaction();
                    $result2 = DB::select('EXEC  RCA_Sincroniza_pedidos_ind ?', array($buscar));
                    if($result2[0]->mensaje == 'OK'){
                         DB::commit();
                     }else{
                         DB::rollBack();
                     }
                 }
                else{
                    DB::rollBack();
                }
            }
            catch(\Exception $e) {
                $array = array('id' => "-1", 'estado' => $e->getMessage(),
                'total' => 0);
                return response()->json($array);
            }
            
        }

        //$pedido = Pedido::where('id', $buscar)->get();
        $pedido = DB::table('pedido_expo')
                        ->leftJoin('ped_expo_mks', 'id', '=', 'ped_expo_mks.id_gpo_expo')
                        ->where('id', $buscar)
                        ->first();
                        //->get();
        //return response()->json($pedido);  


        $array = array('id' =>$pedido->id, 'estado' => $pedido->estado, 'total' => $pedido->num);
        return response()->json($array);
    }

    public function productosPromocionados(Request $request){
        $idmerksyst =  PedidoMks::where('id_gpo_expo', 160)->first();
        //return $idmerksyst->num;
        $claves = array('2857', '27', '4845', '7237', '1466', '3494', '15387', '185' );
        
        $result  = DB::table('invart')
        ->join('inviar', 'invart.art', '=', 'inviar.art')
        ->leftJoin('invmca', 'inviar.marca', '=', 'invmca.marca')
        
        ->leftJoin('codbar', 
            DB::raw('(codbar.art'), 
            '=', DB::raw('invart.art and cant_pre3 = codbar.factor_uds)'))

        /*->leftJoin(
            DB::raw('([TCADBMAB].[dbo].prmdet inner join [TCADBMAB].[dbo].prmhdr 
            on prmhdr.NumProm = prmdet.NumProm)'),
            'invart.art', '=', 'prmdet.cve_art'
            //DB::raw(" prmdet.cve_art and prmhdr.fec_fin >= GETDATE() 
            //AND prmhdr.fec_ini <= GETDATE() and TpoProm = 1 ".
            //" and prmdet.alm = '001' and prmdet.suc = '001' "
            //"and seg_0 = 'EXP'  and prmhdr.status=1")
            )
        */
        ->leftJoin(
            DB::raw('([TCADBMAB].[dbo].prmdet inner join [TCADBMAB].[dbo].prmhdr 
            on prmhdr.NumProm = prmdet.NumProm)'),
            'invart.art', '=', //'prmdet.cve_art'
            DB::raw(" prmdet.cve_art ".
            //" AND prmhdr.fec_fin >= GETDATE() ".
            //" AND prmhdr.fec_ini <= GETDATE() ".
            " AND convert(char(8), getdate(), 112) between fec_ini and fec_fin ".
            " AND TpoProm = 1 ".
            " AND prmdet.alm = '001' ".
            " AND prmdet.suc = '001' " .
            //"and seg_0 = 'EXP' " .
            " AND prmhdr.status=1")
            )
         //Impuestos
         ->leftJoin('comimp',
                    DB::raw('(comimp.clave '),'=', 
                    DB::raw(" invart.imp1 and comimp.modulo = 'V')"))
        ->where('invart.alm', '001')
        //->where('invart.alm', '099')
        ->where('invart.status', '00')
        ->whereIn('invart.art', $claves)
        //->where('des1', 'like', '%'.$buscar.'%')
        //->whereNotNull('TpoProm')
        /*->select('invart.art','cve_pro','des1','inviar.lin','inviar.s_fam',
        'inviar.s_lin','uds_min','cant_pre3', 'TpoProm', 'prmhdr.NumProm as num_prom',
        'uds as ud_pre3', 'invmca.descr as marca','invart.precio_vta0',
        'invart.precio_vta1','invart.precio_vta2','invart.precio_vta3',
        'invart.precio_vta4',
        'cant_pre0','cant_pre1','cant_pre2','cant_pre3','cant_pre4',
        'cant_vol0', 'cant_vol1', 'cant_vol2', 'cant_vol3',
        'prmdet.precio_3 as pr_prm3', 'invart.imp1')
        ->orderByDesc('cve_pro')
        ->distinct()
        ->limit(50)
        ->get();*/
        ->select('invart.art','cve_pro','des1','inviar.lin','inviar.s_fam',
            'inviar.s_lin','uds_min','cant_pre3', 'TpoProm', 'prmhdr.NumProm as num_prom',
            'uds as ud_pre3', 'invmca.descr as marca','invart.precio_vta0',
            'invart.precio_vta1','invart.precio_vta2','invart.precio_vta3',
            'invart.precio_vta4',
            'cant_pre0','cant_pre1','cant_pre2','cant_pre3','cant_pre4',
            'cant_vol0', 'cant_vol1', 'cant_vol2', 'cant_vol3',
            'prmdet.precio_3 as pr_prm3', 'invart.imp1 as imp_1',
            'comimp.imp1 as tasaImp1', 'comimp.imp2 as tasaImp2','comimp.imp3 as tasaImp3')
            ->orderByDesc('cve_pro')
            ->distinct()
            ->limit(50)
            ->get();
        
            
        return response()->json($result);
    }

    public function calculaPrecioSinImpuestos($monto, $tasa1, $tasa2, $tasa3){
        $sinImpuesto1 = doubleval($monto) - (doubleval($monto) / (1 + (doubleval($tasa1)/100)));
        $sinImpuesto2 = doubleval($monto) - (doubleval($monto) / (1 + (doubleval($tasa2)/100)));
        $sinImpuesto3 = doubleval($monto) - (doubleval($monto) / (1 + (doubleval($tasa3)/100)));

        $precioSinImpuestos = $monto - $sinImpuesto1 - $sinImpuesto2 - $sinImpuesto3;
        return $precioSinImpuestos;
    }
}
