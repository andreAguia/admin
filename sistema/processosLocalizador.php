<?php
/**
 * Cadastro de Processos
 * Rotina geral de localiza��o de processos para usu�rio comum
 *  
 * By Alat
 */

# Inicia as vari�veis que receber�o as sessions
$matricula = null;

# Configura��o
include ("../config.php");

# Permiss�o de Acesso
$acesso = Verifica::acesso($matricula);

if($acesso)
{    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();
	
    # Verifica a fase do programa
    $fase = get('fase','listar'); 

    # pega o id (se tiver)
    $id = soNumeros(get('id'));

    # Verifica a paginacao
    $paginacao = get('paginacao',get_session('sessionPaginacao',0));	# Verifica se a pagina��o vem por get, sen�o pega a session
    set_session('sessionPaginacao',$paginacao);

    # Pega o parametro de pesquisa (se tiver)
    if (is_null(post('parametro')))					# Se o parametro n�o vier por post (for nulo)
        $parametro = retiraAspas(get_session('sessionParametro'));	# passa o parametro da session para a variavel parametro retirando as aspas
    else
    { 
        $parametro = post('parametro');			# Se vier por post, retira as aspas e passa para a variavel parametro			
        set_session('sessionParametro',$parametro);		# transfere para a session para poder recuper�-lo depois
        $paginacao = 0;

        # Log da pesquisa
        if($parametro <> '')
        {
            $data = date("Y-m-d H:i:s");
            $atividade = 'Pesquisou processo: '.$parametro;
            $intra->registraLog($matricula,$data,$atividade,'tbprocesso',null);
        }    
    }

    # Ordem da tabela
    $orderCampo = get('orderCampo');
    $orderTipo = get('orderTipo');

    # Come�a uma nova p�gina
    $page = new Page();			
    $page->iniciaPagina();

    # Inicia a classe de interface da �rea do servidor
    $area = new AreaServidor();

    # Cabe�alho da P�gina
    AreaServidor::cabecalho();

    # Exibe o link da Vers�o
    #Intranet::versaoSistema(300,200,'right');

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################

    # Nome do Modelo (aparecer� nos fildset e no caption da tabela)
    $objeto->set_nome('Consulta de Processos');
    $objeto->set_id('Processo');

    # Bot�es
    $objeto->set_voltarLista('areaServidor.php');
    $objeto->set_botaoIncluir(false);

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar:');
    $objeto->set_parametroValue($parametro);
    $objeto->set_exibeLista(false);

    # ordena��o
    if(is_null($orderCampo))
        $orderCampo = 1;

    if(is_null($orderTipo))
        $orderTipo = 'desc';

    # trata o par�metro para busca avan�ada
    $palavras = explode(" ", $parametro);  // separa as palavras e as coloca em um array
    $numPalavras = count($palavras);

    ## Motor de Busca ##

    # Come�a o select
    $select = 'SELECT  numero,
                       data,
                       assunto,
                       idProcesso                           
                  FROM tbprocesso
                  WHERE numero LIKE "%'.$parametro.'%"
                     OR DATE_FORMAT(data,"%d/%m/%Y") LIKE "%'.$parametro.'%"';


    # Entra com as palavras para busca por assunto
    $contador = 1;
    if($numPalavras == 1)
        $select .= ' OR assunto LIKE "%'.$parametro.'%"';
    else 
    {
        $select.='OR (';

         foreach ($palavras as $termos)
         {
             $select.='(assunto LIKE "%'.$termos.'%")';
             if($contador  < $numPalavras)
             {
                 $select.= 'AND';
                 $contador++;
             }
         }

        $select.=')';
    }

    # Finaliza o select com a ordena��o
    $select .= ' ORDER BY '.$orderCampo.' '.$orderTipo;
    #echo $select;

    # select da lista
    $objeto->set_selectLista ($select);

    # ordem da lista
    $objeto->set_orderCampo($orderCampo);
    $objeto->set_orderTipo($orderTipo);
    $objeto->set_orderChamador('?fase=listar');

    # Caminhos
    $objeto->set_linkEditar('?fase=exibeMovimentos');
    $objeto->set_linkListar('?fase=listar');

    # Parametros da tabela
    $objeto->set_label(array("Processo","Data","Assunto"));
    $objeto->set_width(array(20,10,60));
    $objeto->set_align(array("center","center","left"));
    $objeto->set_function(array ("","date_to_php"));

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbprocesso');

    # Nome do campo id
    $objeto->set_idCampo('idProcesso');

    # Nome do bot�o editar
    $objeto->set_nomeColunaEditar('Ver');
    $objeto->set_editarBotao('ver.png');

    # Matr�cula para o Log
    $objeto->set_matricula($matricula);

    # Pagina��o
    $objeto->set_paginacao(true);
    $objeto->set_paginacaoInicial($paginacao);
    $objeto->set_paginacaoItens(13);

    ################################################################

    switch ($fase)
    {
        case "" :
        case "listar" :

            $objeto->listar();

            echo '<br />';
            echo '<br />';

            if($parametro == "")
            {
                $divMensagem = new Box('divDicaProcesso');
                $divMensagem->set_titulo('Como Fazer a Consulta');
                $divMensagem->abre();
                $p = new P('<br/>A consulta aos processos poder� ser efetuada de diversas formas:<br/><br/>
                            1. Pelo n�mero do processo ou parte dele; <br/> 
                            2. Pelo assunto. Exemplo: di�ria, progress�o, licen�a, etc;<br/>
                            3. Pelo nome do requerente ou parte dele;<br/>
                            4. Pelo ano de abertura.<br/><br/>

                            O sistema � novo e ainda n�o tem muitos processos cadastrados. 
                            � poss�vel que a grande maioria das pesquisas ainda n�o encontrem nenhum registro,
                            mas a medida em que o protocolo for cadastrando os processos tramitados o banco de dados ir� aumentar.');

                $p->show();
                $divMensagem->fecha();
            }
            break;   

        case "exibeMovimentos" :
            # Caso tenha permiss�o para editar redireciona para rotina que permite edi��o
            if ($intra->verificaPermissao($matricula,4))
                loadPage ('processosMovimentos.php?processo='.$id);
            else 
            {
                # Bot�o voltar
                Visual::botaoVoltar('?');
                echo '<br />';

                # Exibe os dados do Processo
                $fieldset = new Fieldset('Dados do Processo');
                $fieldset->abre();
                    AreaServidor::listaProcesso($id);
                $fieldset->fecha(); 

                # Exibe as tramita��es desse processo           
                $numTramitacao = $intra->get_tramitacoes($id);
                if ($numTramitacao == 0)
                    $fieldset = new Fieldset('Nenhuma tramita��o');
                elseif($numTramitacao == 1)
                    $fieldset = new Fieldset('1 tramita��o');
                else
                    $fieldset = new Fieldset($numTramitacao.' tramita��es');

                $fieldset->abre();
                    AreaServidor::listaMovimentosProcesso($id);
                $fieldset->fecha(); 
            }

            break;
    }              

    $page->terminaPagina();
}
?>