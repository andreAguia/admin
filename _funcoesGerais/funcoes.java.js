/* 
 * Fun��es em Javascript
 */


/*
* Fun��es de abertura e fechamento (visivel ou n�o) de div
*
* Usado para exibir ou n�o uma div
*/

var aba = null;

function fechaDivId(div)
{
    document.getElementById(div).style.display = "none";
    aba=false;
}

function abreDivId(div)
{ 
    document.getElementById(div).style.display = "block";
    aba=true;
}

function abreFechaDivId(div)
{
    if(aba)
    {
        fechaDivId(div);
    }
    else
    {
        abreDivId(div);
    }
}

/*
* Fun��es para requisi��o ajax de uma p�gina
*
* Usado para carregar uma p�gina dentro de uma div
*/

function createXMLHttpRequest() 
{
    var xmlHttp = false;
    if(window.ActiveXObject)
    {
        xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    else if(window.XMLHttpRequest)
    {
        xmlHttp = new XMLHttpRequest();
    }
    else
    {
        alert("Atualize seu navegador! O navegador atual n�o suporta AJAX!");
    }

    return xmlHttp;
}

function ajaxLoadPage(url, div, parametro)
{
    var xmlhttp = createXMLHttpRequest();
    
    if(parametro != null)
        url = url + parametro;
    
    xmlhttp.open("GET", url, true);
    xmlhttp.onreadystatechange = function() 
    {
        if(xmlhttp.readyState == 4)
            document.getElementById(div).innerHTML = xmlhttp.responseText;
    }
    xmlhttp.send(null);
}

/*
* fun��o de confirma��o do bot�o gr�fico
*
* Usado na rotina do bot�o gr�fico quando o parametro de confirma��o est� true
*/

function confirma(chamador,msg){
	var opcao = confirm(msg);
	if (opcao)
		window.location = chamador;
}

/* 
 * Fun��o de contagem regressiva
 * 
 * Usada na rotina de servi�os para exibir o tempo restante para o refresh da p�gina
 * 
 * @param   valor number  o valor a ser decrementado
 * @param   saida string  o id da div a ser impressa o velor
 * 
 * 
 */

function contagemRegressiva(valor,saida){
    if((valor - 1) >= 0){
        valor = valor - 1;
        //divContagem.innerText = '00:' + valor;
        document.getElementById(saida).innerHTML = valor;
        setTimeout('contagemRegressiva('+valor+',"'+saida+'")',1000);
    }
}

/*
* Java Script da rotina de preenchimento de um input
* 
* Usado na rotina de not�cias para transferir o nome do arquivo da figura para o campo do formul�rio
*/

function fillInput(input,valor){
	input.value = valor;
 }
 
 /*
* fun��o loadPage 
* 
* Carrega uma p�gina por jscript
*/

 function loadPage(url, target, parametros)
{
    if (parametros == undefined)
        parametros = 'menubar=no,scrollbars=yes,location=no,directories=no,status=no,width=750,height=600';
    
    if (target == undefined)
        window.open(url);
    else 
        window.open(url,target,parametros);
}

/*
* fun��o pularCampo 
* 
* habilita a passagem de um campo par outro quando o total de caracteres � atingido
*/

function pularCampo(origem, tamanho, destino)
{
    if(document.getElementById(origem).value.length == tamanho-1)
    {
        document.getElementById(destino).focus();
    }
}
