function XHConn()
{
  var xmlhttp, bComplete = false;
  try { xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); }
  catch (e) { try { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); }
  catch (e) { try { xmlhttp = new XMLHttpRequest(); }
  catch (e) { xmlhttp = false; }}}
  if (!xmlhttp) return null;
  this.connect = function(sURL, sMethod, sVars, fnDone)
  {
    if (!xmlhttp) return false;
    bComplete = false;
    sMethod = sMethod.toUpperCase();

try {
      if (sMethod == "GET")
      {
        xmlhttp.open(sMethod, sURL+"?"+sVars, true);
        sVars = "";
      }
      else
      {
        xmlhttp.open(sMethod, sURL, true);
        xmlhttp.setRequestHeader("Method", "get "+sURL+" HTTP/1.1");
        xmlhttp.setRequestHeader("Content-Type",
          "application/x-www-form-urlencoded");
      }
      xmlhttp.onreadystatechange = function(){
        if (xmlhttp.readyState == 4 && !bComplete)
        {
          bComplete = true;
          fnDone(xmlhttp);
        }};
      xmlhttp.send(sVars);
    }
    catch(z) { return false; }
    return true;
  };
  return this;
}

var myConn = new XHConn();
var carregando = "<center><br />Carregando...<br /><br /><img src='../view/images/loader.gif'></img></center>";

if (!myConn) alert("Erro.");

function opcaoConsulta() {
	var radio = null;
	var valorRadio = "";
	
	for(var i=0; i<document.frm_consulta.opcao.length; i++) {
		if(document.frm_consulta.opcao[i].checked) {
			valorRadio = document.frm_consulta.opcao[i].value;
		}
	}
	
	switch(valorRadio) {
		case "periodo":
			document.getElementById("div_periodo").style.display = "block";
			document.getElementById("div_tipo_inscricao").style.display = "none";
			document.getElementById('resultado').style.display = "none";
			document.getElementById("data_inicio").value = "";
			document.getElementById("data_fim").value = "";
			document.getElementById("div_botao").style.display = "block";
			document.getElementById("data_inicio").focus();
			break;
			
		case "tipo_inscricao":
			document.getElementById("div_tipo_inscricao").style.display = "block";
			document.getElementById("div_periodo").style.display = "none";
			document.getElementById('resultado').style.display = "none";
			document.getElementById("div_botao").style.display = "block";
			break;
			
		case "inscricoes_confirmadas":
			document.getElementById("div_periodo").style.display = "none";
			document.getElementById("div_tipo_inscricao").style.display = "none";
			document.getElementById('resultado').style.display = "none";
			document.getElementById("div_botao").style.display = "none";
			consultar();
			break;
	
		case "inscricoes_confirmadas_periodo":
			document.getElementById("div_periodo").style.display = "block";
			document.getElementById("div_tipo_inscricao").style.display = "none";
			document.getElementById('resultado').style.display = "none";
			document.getElementById("data_inicio").value = "";
			document.getElementById("data_fim").value = "";
			document.getElementById("div_botao").style.display = "block";
			document.getElementById("data_inicio").focus();
			break;
	
		case "ordem_alfabetica":
			document.getElementById("div_periodo").style.display = "none";
			document.getElementById("div_tipo_inscricao").style.display = "none";
			document.getElementById('resultado').style.display = "none";
			document.getElementById("div_botao").style.display = "none";
			consultar();
			break;

		case "inscricoes_canceladas":
			document.getElementById("div_periodo").style.display = "none";
			document.getElementById("div_tipo_inscricao").style.display = "none";
			document.getElementById('resultado').style.display = "none";
			document.getElementById("div_botao").style.display = "none";
			consultar();
			break;
            
		default:
			alert("Escolha uma opcao");
	}
}

function validarPeriodo(data_inicio, data_fim) {
	if(data_inicio.value == "" || data_fim.value == "") {
		alert("Preencha as datas");
		data_inicio.focus();
		exit;
	}
	
	comparaDatas(data_inicio.value, data_fim.value);
}


function consultar() {
	var opcao = "";
	var get = "";
	
	for(var i=0; i<document.frm_consulta.opcao.length; i++) {
		if(document.frm_consulta.opcao[i].checked) {
			valorRadio = document.frm_consulta.opcao[i].value;
		}
	}
	
	switch(valorRadio) {
		case "periodo":
			var data_inicio = document.getElementById('data_inicio');
			var data_fim = document.getElementById('data_fim');
		
			validarPeriodo(data_inicio, data_fim);
			
			get = "opcao="+valorRadio+
				  "&data_inicio="+data_inicio.value+
				  "&data_fim="+data_fim.value;
			break;
			
		case "tipo_inscricao":
			var tipo_inscricao = document.getElementById('tipo_inscricao').value;
			get = "opcao="+valorRadio+
				  "&tipo_inscricao="+tipo_inscricao;
			break;
			
		case "inscricoes_confirmadas":
			get = "opcao="+valorRadio;
			break;
			
		case "inscricoes_confirmadas_periodo":
			var data_inicio = document.getElementById('data_inicio');
			var data_fim = document.getElementById('data_fim');
		
			validarPeriodo(data_inicio, data_fim);
			
			get = "opcao="+valorRadio+
				  "&data_inicio="+data_inicio.value+
				  "&data_fim="+data_fim.value;
			break;
			
		case "ordem_alfabetica":
			get = "opcao="+valorRadio;
			break;

        case "inscricoes_canceladas":
			get = "opcao="+valorRadio;
			break;
	}
	
	var campoResultado = document.getElementById('resultado');
	campoResultado.style.display = 'block';

	var inclusao = function (oXML) { campoResultado.innerHTML = oXML.responseText; };
	campoResultado.innerHTML = carregando;	
	
    myConn.connect("../../inscricao/admin/consultaRelatorios.php", "GET", get, inclusao);
}
