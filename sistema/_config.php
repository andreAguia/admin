<?php
/**
 * Configuração do Sistema de Administração
 * 
 * By Alat
 */

# Sobre o Sistema
define("VERSAO","1.0.02 UENF");                                # Versão do Sistema 								
define("ATUALIZACAO","19/01/2017");                            # Última Atualização
define("SISTEMA","Sistema de Administração de Sistemas");      # Nome do sistema
define("DESCRICAO","Sistema Gestão dos Sistemas");             # Descrição do sistema
define("PALAVRAS_CHAVE","Uenf");                               # Palavras chave para sites de busca
define("AUTOR","Alat");                                        # Autor do sistema

# Classes
define("PASTA_CLASSES_GERAIS","../../_framework/_classesGerais/"); # Classes Gerais
define("PASTA_CLASSES_GRH","../../grh/_classes/");                 # Classes do sistema de Pessoal 
define("PASTA_CLASSES","../_classes/");                            # Classes Específicas

# Funções
define("PASTA_FUNCOES_GERAIS","../../_framework/_funcoesGerais/");  # Funções Gerais
define("PASTA_FUNCOES","../_funcoes/");                             # Funções Específicas
define("PASTA_FUNCOES_GRH","../../grh/_funcoes/");                  # Funções Específicas GRH

# Figuras
define("PASTA_FIGURAS_GERAIS","../../_framework/_imgGerais/");     # Figuras Gerais
define("PASTA_FIGURAS","../_img/");                     # Figuras Específicas

# Estilos
define("PASTA_ESTILOS_GERAIS","../../_framework/_cssGerais/");     # Estilos Gerais (Foundation)
define("PASTA_ESTILOS","../_css/");                     # Estilos Específicos

# Fontes para PDF
define('FPDF_FONTPATH','../../_framework/_pdfFont/');

# Tags aceitas em campos com htmlTag = true
define('TAGS','<p></p><a></a><br/><br><div></div><table></table><tr></tr><td></td><th></th><strong></strong><em></em><u></u><sub></sub><sup></sup><ol></ol><li></li><ul></ul><hr><span></span><h3></h3>');       

# Cria array dos meses
$mes = array(array("1","Janeiro"),
             array("2","Fevereiro"),
             array("3","Março"),
             array("4","Abril"),
             array("5","Maio"),
             array("6","Junho"),
             array("7","Julho"),
             array("8","Agosto"),
             array("9","Setembro"),
             array("10","Outubro"),
             array("11","Novembro"),
             array("12","Dezembro"));

# Inicia a Session
session_start();
session_cache_limiter('private'); 

# Funções	
include_once (PASTA_FUNCOES_GERAIS."funcoes.gerais.php");
include_once (PASTA_FUNCOES."funcoes.especificas.php");
include_once (PASTA_FUNCOES_GRH."funcoes.especificas.php");

# Dados do Browser
$browser = get_BrowserName();
define("BROWSER_NAME",$browser['browser']);	# Nome do browser
define("BROWSER_VERSION",$browser['version']);	# Versão do browser

# Pega o ip da máquina
define("IP",getenv("REMOTE_ADDR"));     # Ip da máquina

# Sistema Operacional
define("SO",get_So());

# Programa Chamador
$arquivo = explode("/",$_SERVER['PHP_SELF']);
$arquivo = end($arquivo);
define("CHAMADOR",$arquivo);

setlocale (LC_ALL, 'pt_BR');
setlocale (LC_CTYPE, 'pt_BR');

# carrega as session
$idUsuario = get_session('idUsuario');                       # Servidor Logado

# Define se usa o input type data do html5 ou se usa o javascript
# Se usar o html 5 o controle não trabalha com formato brasileiro
# mas browsers exibem no format brasileiro ao 'perceber' o idioma do usuário
if(BROWSER_NAME == 'CHROME')
    define('HTML5',true);
else
    define('HTML5',false);

/**
 * Função que é chamada automaticamente pelo sistema
 * para carregar na memória uma classe no exato momento
 * que a classe é instanciada.
 * 
 * @param  $classe = a classe instanciada
 */

function __autoload($classe)
{
    # Verifica se existe essa classe nas classes gerais
    if (file_exists(PASTA_CLASSES_GERAIS."/class.{$classe}.php"))
        include_once PASTA_CLASSES_GERAIS."/class.{$classe}.php"; 
        
    if (file_exists(PASTA_CLASSES_GERAIS."/interface.{$classe}.php"))
        include_once PASTA_CLASSES_GERAIS."/interface.{$classe}.php";
        
    if (file_exists(PASTA_CLASSES_GERAIS."/container.{$classe}.php"))
        include_once PASTA_CLASSES_GERAIS."/container.{$classe}.php";    
        
    if (file_exists(PASTA_CLASSES_GERAIS."/html.{$classe}.php"))
        include_once PASTA_CLASSES_GERAIS."/html.{$classe}.php";
        
    if (file_exists(PASTA_CLASSES_GERAIS."/outros.{$classe}.php"))
        include_once PASTA_CLASSES_GERAIS."/outros.{$classe}.php";     

    if (file_exists(PASTA_CLASSES_GERAIS."/rel.{$classe}.php"))
        include_once PASTA_CLASSES_GERAIS."/rel.{$classe}.php";      
        
    if (file_exists(PASTA_CLASSES_GERAIS."/bd.{$classe}.php"))
        include_once PASTA_CLASSES_GERAIS."/bd.{$classe}.php";          

    # Verifica se existe a classe nas classes específicas
    if (file_exists(PASTA_CLASSES."/class.{$classe}.php"))
        include_once PASTA_CLASSES."/class.{$classe}.php";
        
    if (file_exists(PASTA_CLASSES."/interface.{$classe}.php"))
        include_once PASTA_CLASSES."/interface.{$classe}.php";
        
    # Verifica se existe a classe nas classes do sistema de Administração
    if (file_exists(PASTA_CLASSES_GRH."/class.{$classe}.php"))
        include_once PASTA_CLASSES_GRH."/class.{$classe}.php";
        
    if (file_exists(PASTA_CLASSES_GRH."/interface.{$classe}.php"))
        include_once PASTA_CLASSES_GRH."/interface.{$classe}.php";
}