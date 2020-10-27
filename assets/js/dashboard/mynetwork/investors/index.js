import { getChild } from '../../../utils/networkview'
import { drawChart } from '../../../utils/networkview'

google.charts.load('current', {packages:["orgchart"]});
google.charts.setOnLoadCallback(drawChart);

getChild(userJson, "investor");




