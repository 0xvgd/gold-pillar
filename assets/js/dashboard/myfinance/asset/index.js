import { getChild } from '../../../utils/networkview'
import { drawChart } from '../../../utils/networkview'

google.charts.load('current', {packages:["orgchart"]});
google.charts.setOnLoadCallback(drawChart);

if(userJson.length === 0){
    $('#message').text("No tenants found");
    $('.alert-info').show();
    $('#chart_div').hide();
} else {
    getChild(userJson, "tenant");
}




