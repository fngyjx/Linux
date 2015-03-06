var xmlHttp
var id

function search(script,data)
{
var temp;
xmlHttp=GetXmlHttpObject();
if (xmlHttp==null)
  {
  alert ("Your browser does not support AJAX!");
  return;
  } 
var url=script+".php";
url=url+"?q="+data;
id=update_id;
xmlHttp.onreadystatechange=function () 
{ 
	if (xmlHttp.readyState==4)
	{ 
	temp =  xmlHttp.responseText;
	}
};
xmlHttp.open("GET",url,true);
xmlHttp.send(null);
return temp;
}

function update_id(script,update_id,data)
{ 
xmlHttp=GetXmlHttpObject();
if (xmlHttp==null)
  {
  alert ("Your browser does not support AJAX!");
  return;
  } 
var url=script+".php";
url=url+"?search="+data;
id=update_id;
xmlHttp.onreadystatechange=stateChanged;
xmlHttp.open("GET",url,true);
xmlHttp.send(null);
}

function stateChanged() 
{ 
if (xmlHttp.readyState==4)
{ 
document.getElementById(id).innerHTML=xmlHttp.responseText;
}
}

function GetXmlHttpObject()
{
var xmlHttp=null;
try
  {
  // Firefox, Opera 8.0+, Safari
  xmlHttp=new XMLHttpRequest();
  }
catch (e)
  {
  // Internet Explorer
  try
    {
    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
return xmlHttp;
}

function QuantityConvert(quantity, units_from, units_to) {
	if (!isNaN(quantity) && ("lbs"==units_from || "kg"==units_from || "grams"==units_from ) && ( "lbs"==units_to || "kg"==units_to || "grams"==units_to)) {
		switch (units_from) {
		case "lbs": 
			if ("grams"==units_to) {
				return (quantity * 453.59237).toFixed(5);
			} else if ("kg"==units_to) {
				return (quantity * .45359237).toFixed(5);
			} else { 
				return quantity;
			}
			break;
		case "grams" : 
			if ( "lbs"==units_to) {
				return (quantity / 453.59237).toFixed(5) ;
			} else if ( "kg"==units_to) {
				return (quantity / 1000).toFixed(5);
			} else {
				return quantity;
			}
			break;
		case "kg": 
			if ( "grams"==units_to) {
				return (quantity * 1000).toFixed(5);
			} else if ( "lbs"==units_to) {
				return (quantity / .45359237).toFixed(5);
			} else {
				return quantity;
			}
			break;
		}
	}
	return Number.NaN;
}