import React from 'react';


class CompEx extends React.Component{
	
	 

	render(){
		
		var d = [];
		
		var j = JSON.parse(this.props.devices);
		var s = JSON.parse(this.props.status);
		for(var i = 0;i < j.length;i++){
			var ahref = "./device/" + j[i].device_id + "/" + j[i].st_x + "/" + j[i].st_y;
			var pp = '++';
			if(s[i] == 'no active'){
				ahref = './../devices';
				pp = '--';
			}
			d.push({url: ahref, id: j[i].device_id, status:s[i], pp: pp});

		}
		

		return ( 
			<>
			<div>
			<ul className="navmain_devicesul">
			<li className="navmain_devicesli"> DEVICES </li>
			<li className="navmain_devicesli"><a href="./alldevices/"> All Devices </a></li>
			{d.map((item)=><li className="navmain_devicesli" key={item.id}>
				
				<a className="navmain_deviceslia" href={item.url}>{item.pp} {item.id}</a>
				
				</li>
			)}
			</ul>
			</div>
			</>
		)


	}
	
 
}

export default CompEx;
