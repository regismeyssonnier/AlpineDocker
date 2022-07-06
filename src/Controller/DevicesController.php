<?php

namespace App\Controller;

use App\Entity\Devices;
use App\Repository\DevicesRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DevicesController extends AbstractController
{
    #[Route('/devices', name: 'app_devices')]
    public function index(ManagerRegistry $doc): Response
	    {
		
		$conn = $doc->getManager()->getConnection();

        	$sql = 'select d.device_id, dp.startd, dp.endd, ST_X(dp.geom), ST_Y(dp.geom)
		from devices as d 
		inner join devices_pos as dp on d.device_id = dp.device_id 
		group by d.device_id, dp.startd, dp.endd, dp.geom
		order by d.device_id;';

        	$stmt = $conn->prepare($sql);
        	$resultSet = $stmt->executeQuery();
        	$devid =  $resultSet->fetchAllAssociative();
		//$devid = $doc->getRepository(Devices::class)->findAll();

		$status = [];
		foreach($devid as $d){

			$sql = 'select air_temperature_value, air_temperature_unit, to_timestamp(timestamp_UTC ) , ST_X(dp.geom), ST_Y(dp.geom), startd, endd
			from sensors as s
			inner join devices_pos as dp on dp.device_id = s.device_id
			where s.device_id = ' .$d['device_id'] 
			.' group by air_temperature_value, air_temperature_unit, timestamp_UTC, dp.geom, startd, endd
			having ST_X(dp.geom) = ' .$d['st_x'] .' and ST_Y(dp.geom) = ' .$d['st_y'] .' '
			.'and to_timestamp(timestamp_UTC ) between startd and endd
			order by timestamp_UTC;';

			$stmt = $conn->prepare($sql);
        		$resultSet = $stmt->executeQuery();
        		$dev =  $resultSet->fetchAllAssociative();
			if(count($dev) != 0){
				array_push($status, 'active');
			}
			else
			{
				array_push($status, 'no active');
			}

		}
		
	        return $this->render('devices/index.html.twig', [
	            'controller_name' => 'DevicesController',
		    'devices' => $devid,
		    'status' => $status
	        ]);
	    }

	private function device_id_(ManagerRegistry $doc, $id, $posx, $posy): array
    {
	// position
	$conn = $doc->getManager()->getConnection();
	$sql = 'select d.device_id, dp.startd, dp.endd, ST_X(dp.geom), ST_Y(dp.geom)
		from devices as d 
		inner join devices_pos as dp on d.device_id = dp.device_id 
		where d.device_id = ' .$id
		.' group by d.device_id, dp.startd, dp.endd, dp.geom
		having ST_X(dp.geom) = ' .$posx .' and ST_Y(dp.geom) = ' .$posy
		.' order by d.device_id;';
	

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $devid =  $resultSet->fetchAllAssociative();

	//air temp
	$sql = 'select air_temperature_value, air_temperature_unit, to_timestamp(timestamp_UTC ) , ST_X(dp.geom), ST_Y(dp.geom), startd, endd
	from sensors as s
	inner join devices_pos as dp on dp.device_id = s.device_id
	where s.device_id = ' .$id 
	.' group by air_temperature_value, air_temperature_unit, timestamp_UTC, dp.geom, startd, endd
	having ST_X(dp.geom) = ' .$posx .' and ST_Y(dp.geom) = ' .$posy .' '
	.'and to_timestamp(timestamp_UTC ) between startd and endd '
	.'order by timestamp_UTC;';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $airtemp =  $resultSet->fetchAllAssociative();

	//air humidity
	$sql = 'select air_humidity_value, air_humidity_unit, to_timestamp(timestamp_UTC ) , ST_X(dp.geom), ST_Y(dp.geom), startd, endd
	from sensors as s
	inner join devices_pos as dp on dp.device_id = s.device_id
	where s.device_id = ' .$id 
	.' group by air_humidity_value, air_humidity_unit, timestamp_UTC, dp.geom, startd, endd
	having ST_X(dp.geom) = ' .$posx .' and ST_Y(dp.geom) = ' .$posy .' '
	.'and to_timestamp(timestamp_UTC ) between startd and endd '
	.'order by timestamp_UTC;';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $airhum =  $resultSet->fetchAllAssociative();

	//C02 concentration
	$sql = 'select CO2_concentration_value, CO2_concentration_unit, to_timestamp(timestamp_UTC ) , ST_X(dp.geom), ST_Y(dp.geom), startd, endd
	from sensors as s
	inner join devices_pos as dp on dp.device_id = s.device_id
	where s.device_id = ' .$id 
	.' group by CO2_concentration_value, CO2_concentration_unit, timestamp_UTC, dp.geom, startd, endd
	having ST_X(dp.geom) = ' .$posx .' and ST_Y(dp.geom) = ' .$posy .' '
	.'and to_timestamp(timestamp_UTC ) between startd and endd '
	.'order by timestamp_UTC;';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $co2con =  $resultSet->fetchAllAssociative();

	//Pressure
	$sql = 'select Barometric_pressure_value, Barometric_pressure_unit, to_timestamp(timestamp_UTC ) , ST_X(dp.geom), ST_Y(dp.geom), startd, endd
	from sensors as s
	inner join devices_pos as dp on dp.device_id = s.device_id
	where s.device_id = ' .$id 
	.' group by Barometric_pressure_value, Barometric_pressure_unit, timestamp_UTC, dp.geom, startd, endd
	having ST_X(dp.geom) = ' .$posx .' and ST_Y(dp.geom) = ' .$posy .' '
	.'and to_timestamp(timestamp_UTC ) between startd and endd '
	.'order by timestamp_UTC;';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $press =  $resultSet->fetchAllAssociative();

	//avg air temp
	$sql = 'select avg(tmp.atv), tmp.atu from (select air_temperature_value as atv, air_temperature_unit as atu, to_timestamp(timestamp_UTC ) , ST_X(dp.geom), ST_Y(dp.geom), startd, endd
	from sensors as s
	inner join devices_pos as dp on dp.device_id = s.device_id
	where s.device_id = ' .$id 
	.' group by atv, atu, timestamp_UTC, dp.geom, startd, endd
	having ST_X(dp.geom) = ' .$posx .' and ST_Y(dp.geom) = ' .$posy .' '
	.'and to_timestamp(timestamp_UTC ) between startd and endd '
	.'order by timestamp_UTC)tmp
	group by tmp.atu;';

	$stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $avgtemp =  $resultSet->fetchAllAssociative();

	//avg air humidity
	$sql = 'select avg(tmp.ahv), tmp.ahu from(select air_humidity_value as ahv, air_humidity_unit as ahu, to_timestamp(timestamp_UTC ) , ST_X(dp.geom), ST_Y(dp.geom), startd, endd
	from sensors as s
	inner join devices_pos as dp on dp.device_id = s.device_id
	where s.device_id = ' .$id 
	.' group by ahv, ahu, timestamp_UTC, dp.geom, startd, endd
	having ST_X(dp.geom) = ' .$posx .' and ST_Y(dp.geom) = ' .$posy .' '
	.'and to_timestamp(timestamp_UTC ) between startd and endd '
	.'order by timestamp_UTC) tmp
	group by tmp.ahu;';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $avghum =  $resultSet->fetchAllAssociative();

	//avg C02 concentration
	$sql = 'select avg(tmp.ccv), tmp.ccu from(select CO2_concentration_value as ccv, CO2_concentration_unit as ccu, to_timestamp(timestamp_UTC ) , ST_X(dp.geom), ST_Y(dp.geom), startd, endd
	from sensors as s
	inner join devices_pos as dp on dp.device_id = s.device_id
	where s.device_id = ' .$id 
	.' group by ccv, ccu, timestamp_UTC, dp.geom, startd, endd
	having ST_X(dp.geom) = ' .$posx .' and ST_Y(dp.geom) = ' .$posy .' '
	.'and to_timestamp(timestamp_UTC ) between startd and endd '
	.'order by timestamp_UTC) tmp
	group by tmp.ccu;';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $avgco2 =  $resultSet->fetchAllAssociative();

	//avg Pressure
	$sql = 'select avg(tmp.bpv), tmp.bpu from (select Barometric_pressure_value as bpv, Barometric_pressure_unit as bpu, to_timestamp(timestamp_UTC ) , ST_X(dp.geom), ST_Y(dp.geom), startd, endd
	from sensors as s
	inner join devices_pos as dp on dp.device_id = s.device_id
	where s.device_id = ' .$id 
	.' group by Barometric_pressure_value, Barometric_pressure_unit, timestamp_UTC, dp.geom, startd, endd
	having ST_X(dp.geom) = ' .$posx .' and ST_Y(dp.geom) = ' .$posy .' '
	.'and to_timestamp(timestamp_UTC ) between startd and endd '
	.'order by timestamp_UTC) tmp
	group by tmp.bpu;';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $avgpress =  $resultSet->fetchAllAssociative();
	
	

	return  [
            'controller_name' => 'DevicesController',
            'device' => $devid,
		    'air_temp' => $airtemp,
		    'air_hum' => $airhum,
		    'co2_conc' => $co2con,
		    'pressure' => $press,
		    'avg_temp' => $avgtemp,
		    'avg_hum' => $avghum,
		    'avg_co2' => $avgco2,
		    'avg_press' => $avgpress
                ];

    }

    #[Route('/device/{id}/{posx}/{posy}', name: 'app_device')]
    public function device_id(ManagerRegistry $doc, $id, $posx, $posy): Response
    {
	// position
	$conn = $doc->getManager()->getConnection();
	$sql = 'select d.device_id, dp.startd, dp.endd, ST_X(dp.geom), ST_Y(dp.geom)
		from devices as d 
		inner join devices_pos as dp on d.device_id = dp.device_id 
		where d.device_id = ' .$id
		.' group by d.device_id, dp.startd, dp.endd, dp.geom
		having ST_X(dp.geom) = ' .$posx .' and ST_Y(dp.geom) = ' .$posy
		.' order by d.device_id;';
	

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $devid =  $resultSet->fetchAllAssociative();

	//air temp
	$sql = 'select air_temperature_value, air_temperature_unit, to_timestamp(timestamp_UTC ) , ST_X(dp.geom), ST_Y(dp.geom), startd, endd
	from sensors as s
	inner join devices_pos as dp on dp.device_id = s.device_id
	where s.device_id = ' .$id 
	.' group by air_temperature_value, air_temperature_unit, timestamp_UTC, dp.geom, startd, endd
	having ST_X(dp.geom) = ' .$posx .' and ST_Y(dp.geom) = ' .$posy .' '
	.'and to_timestamp(timestamp_UTC ) between startd and endd '
	.'order by timestamp_UTC;';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $airtemp =  $resultSet->fetchAllAssociative();

	//air humidity
	$sql = 'select air_humidity_value, air_humidity_unit, to_timestamp(timestamp_UTC ) , ST_X(dp.geom), ST_Y(dp.geom), startd, endd
	from sensors as s
	inner join devices_pos as dp on dp.device_id = s.device_id
	where s.device_id = ' .$id 
	.' group by air_humidity_value, air_humidity_unit, timestamp_UTC, dp.geom, startd, endd
	having ST_X(dp.geom) = ' .$posx .' and ST_Y(dp.geom) = ' .$posy .' '
	.'and to_timestamp(timestamp_UTC ) between startd and endd '
	.'order by timestamp_UTC;';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $airhum =  $resultSet->fetchAllAssociative();

	//C02 concentration
	$sql = 'select CO2_concentration_value, CO2_concentration_unit, to_timestamp(timestamp_UTC ) , ST_X(dp.geom), ST_Y(dp.geom), startd, endd
	from sensors as s
	inner join devices_pos as dp on dp.device_id = s.device_id
	where s.device_id = ' .$id 
	.' group by CO2_concentration_value, CO2_concentration_unit, timestamp_UTC, dp.geom, startd, endd
	having ST_X(dp.geom) = ' .$posx .' and ST_Y(dp.geom) = ' .$posy .' '
	.'and to_timestamp(timestamp_UTC ) between startd and endd '
	.'order by timestamp_UTC;';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $co2con =  $resultSet->fetchAllAssociative();

	//Pressure
	$sql = 'select Barometric_pressure_value, Barometric_pressure_unit, to_timestamp(timestamp_UTC ) , ST_X(dp.geom), ST_Y(dp.geom), startd, endd
	from sensors as s
	inner join devices_pos as dp on dp.device_id = s.device_id
	where s.device_id = ' .$id 
	.' group by Barometric_pressure_value, Barometric_pressure_unit, timestamp_UTC, dp.geom, startd, endd
	having ST_X(dp.geom) = ' .$posx .' and ST_Y(dp.geom) = ' .$posy .' '
	.'and to_timestamp(timestamp_UTC ) between startd and endd '
	.'order by timestamp_UTC;';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $press =  $resultSet->fetchAllAssociative();

	//avg air temp
	$sql = 'select avg(tmp.atv), tmp.atu from (select air_temperature_value as atv, air_temperature_unit as atu, to_timestamp(timestamp_UTC ) , ST_X(dp.geom), ST_Y(dp.geom), startd, endd
	from sensors as s
	inner join devices_pos as dp on dp.device_id = s.device_id
	where s.device_id = ' .$id 
	.' group by atv, atu, timestamp_UTC, dp.geom, startd, endd
	having ST_X(dp.geom) = ' .$posx .' and ST_Y(dp.geom) = ' .$posy .' '
	.'and to_timestamp(timestamp_UTC ) between startd and endd '
	.'order by timestamp_UTC)tmp
	group by tmp.atu;';

	$stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $avgtemp =  $resultSet->fetchAllAssociative();

	//avg air humidity
	$sql = 'select avg(tmp.ahv), tmp.ahu from(select air_humidity_value as ahv, air_humidity_unit as ahu, to_timestamp(timestamp_UTC ) , ST_X(dp.geom), ST_Y(dp.geom), startd, endd
	from sensors as s
	inner join devices_pos as dp on dp.device_id = s.device_id
	where s.device_id = ' .$id 
	.' group by ahv, ahu, timestamp_UTC, dp.geom, startd, endd
	having ST_X(dp.geom) = ' .$posx .' and ST_Y(dp.geom) = ' .$posy .' '
	.'and to_timestamp(timestamp_UTC ) between startd and endd '
	.'order by timestamp_UTC) tmp
	group by tmp.ahu;';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $avghum =  $resultSet->fetchAllAssociative();

	//avg C02 concentration
	$sql = 'select avg(tmp.ccv), tmp.ccu from(select CO2_concentration_value as ccv, CO2_concentration_unit as ccu, to_timestamp(timestamp_UTC ) , ST_X(dp.geom), ST_Y(dp.geom), startd, endd
	from sensors as s
	inner join devices_pos as dp on dp.device_id = s.device_id
	where s.device_id = ' .$id 
	.' group by ccv, ccu, timestamp_UTC, dp.geom, startd, endd
	having ST_X(dp.geom) = ' .$posx .' and ST_Y(dp.geom) = ' .$posy .' '
	.'and to_timestamp(timestamp_UTC ) between startd and endd '
	.'order by timestamp_UTC) tmp
	group by tmp.ccu;';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $avgco2 =  $resultSet->fetchAllAssociative();

	//avg Pressure
	$sql = 'select avg(tmp.bpv), tmp.bpu from (select Barometric_pressure_value as bpv, Barometric_pressure_unit as bpu, to_timestamp(timestamp_UTC ) , ST_X(dp.geom), ST_Y(dp.geom), startd, endd
	from sensors as s
	inner join devices_pos as dp on dp.device_id = s.device_id
	where s.device_id = ' .$id 
	.' group by Barometric_pressure_value, Barometric_pressure_unit, timestamp_UTC, dp.geom, startd, endd
	having ST_X(dp.geom) = ' .$posx .' and ST_Y(dp.geom) = ' .$posy .' '
	.'and to_timestamp(timestamp_UTC ) between startd and endd '
	.'order by timestamp_UTC) tmp
	group by tmp.bpu;';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $avgpress =  $resultSet->fetchAllAssociative();
	

	return $this->render('devices/device.html.twig', [
                    'controller_name' => 'DevicesController',
                    'device' => $devid,
		    'air_temp' => $airtemp,
		    'air_hum' => $airhum,
		    'co2_conc' => $co2con,
		    'pressure' => $press,
		    'avg_temp' => $avgtemp,
		    'avg_hum' => $avghum,
		    'avg_co2' => $avgco2,
		    'avg_press' => $avgpress
                ]);

    }


    #[Route('/alldevices', name: 'app_alldevices')]
    public function device_all(ManagerRegistry $doc): Response
    {
	//get devices
	$conn = $doc->getManager()->getConnection();

    $sql = 'select d.device_id, dp.startd, dp.endd, ST_X(dp.geom), ST_Y(dp.geom)
	from devices as d 
	inner join devices_pos as dp on d.device_id = dp.device_id 
	group by d.device_id, dp.startd, dp.endd, dp.geom
	order by d.device_id;';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $devid =  $resultSet->fetchAllAssociative();
	//$devid = $doc->getRepository(Devices::class)->findAll();

	$devices = [];
	$status = [];
	$miniairtemp = 1000000;
	$avgtemp = 0;
	$avghum = 0;
	$avgco2 = 0;
	$avgpress = 0;
	$index = 0;
	$nb_devact = 0;
	foreach($devid as $d){

		$sql = 'select air_temperature_value, air_temperature_unit, to_timestamp(timestamp_UTC ) , ST_X(dp.geom), ST_Y(dp.geom), startd, endd
		from sensors as s
		inner join devices_pos as dp on dp.device_id = s.device_id
		where s.device_id = ' .$d['device_id'] 
		.' group by air_temperature_value, air_temperature_unit, timestamp_UTC, dp.geom, startd, endd
		having ST_X(dp.geom) = ' .$d['st_x'] .' and ST_Y(dp.geom) = ' .$d['st_y'] .' '
		.'and to_timestamp(timestamp_UTC ) between startd and endd
		order by timestamp_UTC;';

		$stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $dev =  $resultSet->fetchAllAssociative();


		$arr = $this->device_id_($doc, $d['device_id'], $d['st_x'], $d['st_y']);
		array_push($devices, $arr);

		if(count($dev) != 0){
			array_push($status, 'active');
			if(count($arr['air_temp']) < $miniairtemp){
				$miniairtemp = count($arr['air_temp']);

			}

			$avgtemp += $arr['avg_temp'][0]['avg'];
			$avghum += $arr['avg_hum'][0]['avg'];
			$avgco2 += $arr['avg_co2'][0]['avg'];
			$avgpress += $arr['avg_press'][0]['avg'];

			$nb_devact++;
		}
		else
		{
			array_push($status, 'no active');
		}
		
		
		

		$index++;
	
	}
		
	$avgtemp = $avgtemp / $nb_devact;
	$avghum = $avghum / $nb_devact;
	$avgco2 = $avgco2 / $nb_devact;
	$avgpress = $avgpress / $nb_devact;

    return $this->render('devices/deviceall.html.twig', [
		'devices' => $devices,
		'status' => $status,
		'count_temp' => $miniairtemp,
		'avg_temp' => $avgtemp,
		'avg_hum' => $avghum,
		'avg_co2' => $avgco2,
		'avg_press' => $avgpress
	]);


    }


	
}

?>
