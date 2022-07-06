import React from 'react';


class CompDev extends React.Component{


        render(){

                var d = [], x = [], y = [];
			
                var j = JSON.parse(this.props.device);
                /*for(var i = 0;i < j.length;i++){
                        d.push({id:j[i].device_id, x: j[i].st_x, y: j[i].st_y});
			//x.push(j[i].st_x);
			//y.push(j[i].st_y);
		}*/
                return (
                        
                        <div className="box_device_devpos" key={j[0].device_id}>
             		                              
                             <h2 style={{ height:'20px', marginBottom:'0px' }}>++ Device {j[0].device_id}++</h2>
                             <h3>Position: {j[0].st_x}-{j[0].st_y}</h3>
                                                               
                        </div>
                     
                )


        }


}

export default CompDev;
