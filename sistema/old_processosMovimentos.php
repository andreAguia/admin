<?php
/**
 * Cadastro de Movimentos de Processos
 *  
 * By Alat
 */

# Reservado para a matr�cula do servidor logado
$matricula = null; 

# Configura��o
include ("../config.php");

# Permiss�o de Acesso
$acesso = Verifica::acesso($matricula,4);
#$acesso = Verifica::acesso($matricula,8); // essa rotina dever� permitir acesso a regra 8(futuro)

if($acesso)
{    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();
	
    # Verifica a fase do programa
    $fase = get('fase','listarMovimentos');

    # pega o processo
    $processo = get('processo');
    if(is_null($processo))
        $processo = get_session ('sessionProcesso');
    else
        set_session('sessionProcesso',$processo); 

    # pega o n�mero de tramita��es desse processo
    $numTramitacao = $intra->get_tramitacoes($processo);

    # Insere jscript extra de valida��o dos campos de inclus�o de tramita��o
    $jscript = '<script language="JavaScript" >

                function enviardados()
                {
                    // valida se a data est� em branco
                    if(document.tramita.data.value=="")
                    {
                        alert( "O campo DATA est� vazio!" );                    
                        return false;
                    }

                    // valida se a origem est� em branco
                    if((document.tramita.origem.value=="") && (document.tramita.origemExterna.value==""))
                    {
                       alert( "O campo Origem est� vazio!" );                   
                       return false;
                    }

                    // valida se o destino est� em branco
                    if((document.tramita.destino.value=="") && (document.tramita.destinoExterno.value==""))
                    {
                        alert( "O campo Destino est� vazio!" );
                        return false;
                    }

                    // valida se o destino est� igual ao origem
                    if(document.tramita.destino.value == document.tramita.origem.value)
                    {
                        alert( "O processo n�o pode entrar e sair de um mesmo setor!" );
                        return false;
                    }

                    return true;
                }
                </script>';

    # Come�a uma nova p�gina
    $page = new Page();

    if($numTramitacao<>0)
        $page->set_jscript($jscript);
    $page->iniciaPagina();

    # Cabe�alho da P�gina
    AreaServidor::cabecalho();

    #####################################

    # Rotina de altera��o do assunto do processo
    $formProcesso = new Modelo();
    $formProcesso->set_selectEdita('SELECT numero,data,assunto FROM tbprocesso WHERE idProcesso = '.$processo);
    $formProcesso->set_linkGravar('?fase=editaProcesso');
    $formProcesso->set_classBd('Intra');
    $formProcesso->set_tabela('tbprocesso');
    $formProcesso->set_idCampo('idProcesso');
    $formProcesso->set_formlabelTipo(1);
    $formProcesso->set_nomeForm('processo');
    $formProcesso->set_botaoVoltarForm(false);
    $formProcesso->set_botaoHistorico(false);
    $formProcesso->set_botaoSalvarGrafico(false);

    # Verifica se pode alterar ou n�o o n�mero e a data do processo
    if($matricula == GOD)
        $readonly = false;
    else
        $readonly = true;

    $formProcesso->set_campos(array(array('nome' => 'numero',
                                          'label' => 'Processo:',
                                          'tipo' => 'texto',
                                          'readOnly' => $readonly,
                                          'size' => 30,
                                          'title' => 'N�mero do Processo',
                                          'linha' => 1),
                                    array('nome' => 'data',
                                          'label' => 'Data:',
                                          'tipo' => 'date',
                                          'readOnly' => $readonly,
                                          'size' => 20,
                                          'title' => 'Data inicial do Processo',
                                          'linha' => 1),
                                    array('nome' => 'assunto',
                                          'label' => 'Assunto:',
                                          'tipo' => 'textarea',
                                          'size' => array(60,5),
                                          'title' => 'Assunto',
                                          'linha' => 2)));
    $formProcesso->set_matricula($matricula);

    #####################################

    switch ($fase)
    {
        case "" :
        case "listarMovimentos" :
            /**
            * Fase que lista os movimentos de um processo 
            */

            # Bot�o voltar
            Visual::botaoVoltar('areaServidor.php');            

            # Bot�o de Tramita��o (para os usu�rios com direito a tramitar)
            if ($intra->verificaPermissao($matricula,4))
            {
                # Menu
                $fieldset = new Fieldset('Termos:');
                $fieldset->abre();
                $menu = new MenuGrafico(6);
            
                #$botao = new BotaoGrafico();
                $botao = new Button();
                $botao->set_label('Abertura de Processo');
                $botao->set_title('Termo de Abertura do Processo: '.$intra->get_numeroProcesso($processo));
                $botao->set_url('../relatorios/termoAberturaProcesso.php?processo='.$processo);
                $botao->set_target('_blank');			
                #$botao->set_image(PASTA_FIGURAS.'termoAberturaProcesso.png',90,50);                     
                $menu->add_item($botao);

                #$botao = new BotaoGrafico();
                $botao = new Button();
                $botao->set_label('Encerramento de Processo');
                $botao->set_title('Termo de Encerramento do Processo: '.$intra->get_numeroProcesso($processo));
                $botao->set_url('../relatorios/termoEncerramentoProcesso.php?processo='.$processo);
                $botao->set_target('_blank');			
                #$botao->set_image(PASTA_FIGURAS.'termoEncerramentoProcesso.png',90,50);                     
                $menu->add_item($botao);

                #$botao = new BotaoGrafico();
                $botao = new Button();
                $botao->set_label('Desarquivamento de Processo');
                $botao->set_title('Termo de Desarquivamento de Processo: '.$intra->get_numeroProcesso($processo));
                $botao->set_url('../relatorios/termoDesarquivamentoProcesso.php?processo='.$processo);
                $botao->set_target('_blank');			
                #$botao->set_image(PASTA_FIGURAS.'termoDesarquivamentoProcesso.png',90,50);                     
                $menu->add_item($botao);
                
                #$botao = new BotaoGrafico();
                $botao = new Button();
                $botao->set_label('Abertura de Volume');
                $botao->set_title('Termo de Abertura de Volume: '.$intra->get_numeroProcesso($processo));
                $botao->set_url('../relatorios/termoAberturaVolume.php?processo='.$processo);
                $botao->set_target('_blank');			
                #$botao->set_image(PASTA_FIGURAS.'termoDesarquivamentoProcesso.png',90,50);                     
                $menu->add_item($botao);
                
                #$botao = new BotaoGrafico();
                $botao = new Button();
                $botao->set_label('Encerramento de Volume');
                $botao->set_title('Termo de Encerramento de Volume: '.$intra->get_numeroProcesso($processo));
                $botao->set_url('../relatorios/termoEncerramentoVolume.php?processo='.$processo);
                $botao->set_target('_blank');			
                #$botao->set_image(PASTA_FIGURAS.'termoDesarquivamentoProcesso.png',90,50);                     
                $menu->add_item($botao);
                
                #$botao = new BotaoGrafico();
                $botao = new Button();
                $botao->set_label('Apensa��o de Processo');
                $botao->set_title('Termo de Apensa��o de Process: '.$intra->get_numeroProcesso($processo));
                $botao->set_url('../relatorios/termoApensacaoProcesso.php?processo='.$processo);
                $botao->set_target('_blank');			
                #$botao->set_image(PASTA_FIGURAS.'termoDesarquivamentoProcesso.png',90,50);                     
                $menu->add_item($botao);
                $menu->show();
                $fieldset->fecha();
            }
            
            # Bot�o hist�rico somente para o GOD
            if ($matricula == GOD)
            {        
                $menu = new MenuGrafico(5,'botaoHistorico');
                #$botao = new BotaoGrafico();
                $botao = new Button();
                $botao->set_label('Hist�rico'); 
                $botao->set_title('Acessa o hist�rico desse registro');
                $botao->set_onClick("abreFechaDivId('divHistorico');");           
                #$botao->set_image(PASTA_FIGURAS.'botaoHistorico.png',90,50);                    
                $menu->add_item($botao);
                $menu->show();
            }            
            
            $menu = new MenuGrafico(5,'movimentoProcessoMenu');
            # Bot�o pesquisar outro processo
            #$botao = new BotaoGrafico();
            $botao = new Button();
            $botao->set_label('Nova<br/>Pesquisa');            
            #$botao->set_image(PASTA_FIGURAS.'botaoNovaPesquisa.png',90,50);  
            $botao->set_url('processosProtocolo.php');
            $menu->add_item($botao);
            
            # Bot�o de edi��o do processo
            #$botao = new BotaoGrafico();
            $botao = new Button();
            $botao->set_label('Editar<br/>Processo');
            if($matricula == GOD)
                $botao->set_onClick("abreDivId('divProcessoMovimento'); document.forms[0].elements[0].focus();");
            else
                $botao->set_onClick("abreDivId('divProcessoMovimento'); document.forms[0].elements[2].focus();");
            
            $menu->add_item($botao);

            # Bot�o de Tramita��o (para os usu�rios com direito a tramitar)
            if ($intra->verificaPermissao($matricula,4))
            {
                #$botao = new BotaoGrafico();
                $botao = new Button();
                $botao->set_label('Tramitar<br/>Processo'); 
                $botao->set_title('Tramitar o Processo '.$intra->get_numeroProcesso($processo));
                $botao->set_onClick("abreDivId('divTramitacaoProcesso'); document.forms[1].elements[1].focus();");        
                #$botao->set_image(PASTA_FIGURAS.'botaoTramitar.png',90,50);                     
                $menu->add_item($botao);

                if($numTramitacao == 0)
                {
                    #$botao = new BotaoGrafico();
                    $botao = new Button();
                    $botao->set_label('Excluir<br/>Processo'); 
                    $botao->set_title('Exclui o Processo '.$intra->get_numeroProcesso($processo));
                    $botao->set_confirma('Deseja mesmo excluir esse processo?');
                    $botao->set_url('?fase=excluirProcesso');
                    #$botao->set_image(PASTA_FIGURAS.'botaoExcluirProcesso.png',90,50);                   
                    $menu->add_item($botao);
                }
            }

            $menu->show();

            ##################################################
            
            # Exibe os dados do Processo
            $fieldset = new Fieldset('Dados do Processo');
            $fieldset->abre();
                AreaServidor::listaProcesso($processo);
            $fieldset->fecha();
            
            ##################################################

            # Div do hist�rico (log)
            $div = new Div('divHistorico');
            $div->set_title('Hist�rico');
            $div->set_display('none');
            $div->abre();

            $fieldset = new Fieldset('Hist�rico de Altera��es');
            $fieldset->abre();

            $select = 'SELECT tblog.data,
                              tblog.matricula,
                              tbpessoa.nome,
                              tblog.atividade,
                              tblog.idValor
                         FROM intra.tblog 
                    LEFT JOIN pessoal.tbfuncionario ON intra.tblog.matricula = pessoal.tbfuncionario.matricula
                    LEFT JOIN pessoal.tbpessoa ON pessoal.tbfuncionario.idpessoa = pessoal.tbpessoa.idpessoa 
                        WHERE tblog.tabela="tbprocesso"
                          AND tblog.idValor='.$processo.' order by tblog.data desc';								

            # Conecta com o banco de dados
            $intra = new Intra();
            $result = $intra->select($select);
            $contadorHistorico = $intra->count($select); 

            # Parametros da tabela
            $label = array("Data","Matr�cula","Nome","Atividade","id");
            $width = array(13,10,22,50,5);	
            $align = array("center","center","center","left");
            $funcao = array ("datetime_to_php","dv",null,"htmlspecialchars");								

            # Monta a tabela
            $tabela = new Tabela();
            $tabela->set_conteudo($result);
            $tabela->set_label($label,$width,$align);
            $tabela->set_funcao($funcao);
            #$tabela->set_titulo($titulo);

            $tabela->show();		

            $fieldset->fecha();
            br();
            $div->fecha();

            # Exibe as tramita��es desse processo      
            if ($numTramitacao == 0)
                $fieldset = new Fieldset('Nenhuma tramita��o');
            elseif($numTramitacao == 1)
                $fieldset = new Fieldset('1 tramita��o');
            else
                $fieldset = new Fieldset($numTramitacao.' tramita��es');

             $fieldset->abre();

            # Rotina de exclus�o
            if ($matricula == GOD)
                AreaServidor::listaMovimentosProcesso($processo,TRUE,'?fase=excluirTramitacao');
            else
                AreaServidor::listaMovimentosProcesso($processo);
            $fieldset->fecha();

            #####################################       

            # Editar Assunto do Processo
            $Window = new Box('divProcessoMovimento');
            $Window->set_titulo('Editar o Assunto do Processo');        
            $Window->abre();							
                $formProcesso->editar($processo);				
            $Window->fecha();

            #####################################

            # Rotina de Inser��o de uma tramita��o 

            # Inicia a vari�vel que vai guardar a situa��o do setor origem e destino sendo:
            // 1 -> processo com origem de setor da fenorte
            // 2 -> processo com origem setor externo
            // 3 -> processo sem origem (primeira tramita��o)
            $statusProcesso = 0;

            # Verifica se o processo j� tem carga e pega a 'lota��o atual'  do processo (id)
            $lotacaoAtual = $intra->get_ultimaCarga($processo);

            if(!is_null($lotacaoAtual))
            {   # Se j� tiver carga

                # Verifica se a �ltima carga � um setor da fenorte ou setor extreno
                if(is_numeric($lotacaoAtual)) // se for num�rico � um setor da fenorte    
                {
                    # Fixa os itens da combo origem com somente 1 elemento: o setor da carga
                    $resultOrigem = array(array($lotacaoAtual,$servidor->get_nomelotacao($lotacaoAtual))); 

                    # retira da combo destino o setor origem
                    $result = $servidor->select('SELECT idlotacao, concat(UADM,"-",DIR,"-",GER) as lotacao
                                                  FROM tblotacao
                                                 WHERE ativo = "Sim"
                                                   AND idlotacao <> "'.$lotacaoAtual.'"
                                              ORDER BY 2');

                    # Adiciona o valor de nulo
                    array_push($result,array(null,null)); 

                    # muda status para 1
                    $statusProcesso = 1;   

                }
                else // se n�o for num�rico � um setor de fora da Fenorte (sem id) 
                {   
                    # Coloca o texto de Origem o setor mas n�o fixa pois pode ter alterado
                    $resultOrigem = $lotacaoAtual;

                    # Define a combo destino
                    $result = $servidor->select('SELECT idlotacao, concat(UADM,"-",DIR,"-",GER) as lotacao
                                               FROM tblotacao
                                              WHERE ativo = "Sim"
                                           ORDER BY 2');

                    # Adiciona o valor de nulo
                    array_push($result,array(null,null));  

                    # muda status para 2
                    $statusProcesso = 2; 

                }       

            }
            else // se n�o tiver carga atual (primeira carga)
            {
                 $result = $servidor->select('SELECT idlotacao, concat(UADM,"-",DIR,"-",GER) as lotacao
                                               FROM tblotacao
                                              WHERE ativo = "Sim"
                                           ORDER BY 2');
                array_push($result,array(null,null)); # Adiciona o valor de nulo
                $resultOrigem = $result;

                # muda status para 3
                $statusProcesso = 3; 
            }

            # Abre a janela
            $Window = new Box('divTramitacaoProcesso');
            $Window->set_titulo('Tramitar Processo');
            $Window->abre();

                # Cria o Formul�rio

                $form = new Form('?fase=inserirTramitacao','tramita');
                $form->set_onSubmit("return enviardados();");        // insere rotina extra em jscript
                $form->set_foco('numeroProcesso');

                    # N�mero do Processo
                    $controle = new Input('processo','hidden');
                    $controle->set_valor($processo);
                    $controle->set_linha(1);
                    $form->add_item($controle);

                    # Data de Abertura
                    $controle = new Input('data','date','Data:',1);
                    $controle->set_size(20);
                    $controle->set_valor(date("d/m/Y"));                
                    $controle->set_linha(2);
                    $controle->set_title('Data de Abertura do Processo');
                    $form->add_item($controle);

                    # Origem de setor de dentro da FENORTE (tramita��o interna)
                    # Se for origem da fenorte ou origem nula
                    if (($statusProcesso == 1) or ($statusProcesso == 3))
                    {
                        $controle = new Input('origem','combo','Setor Origem da FENORTE:',1);
                        $controle->set_size(50);
                        $controle->set_array($resultOrigem);
                        $controle->set_valor($lotacaoAtual);                    
                        $controle->set_linha(3);
                        $controle->set_title('Setor Origem');
                        $form->add_item($controle);

                    }

                    # Origem de setor fora da FENORTE (tramita��o externa)
                    # se for origem externa ou origem nula
                    if (($statusProcesso == 2) or ($statusProcesso == 3))
                    {
                        $controle = new Input('origemExterna','texto','Setor Origem Fora da FENORTE:',1);
                        $controle->set_size(50);
                        $controle->set_valor($lotacaoAtual);                    
                        $controle->set_linha(3);
                        $controle->set_title('Setor Origem de Fora da Fenorte');
                        $form->add_item($controle);
                    }

                    # Destino de setor de dentro da FENORTE (tramita��o interna)
                    $controle = new Input('destino','combo','Setor Destino da FENORTE:',1);
                    $controle->set_size(50);
                    $controle->set_array($result);                
                    $controle->set_linha(4);
                    $controle->set_title('Setor Destino');
                    $form->add_item($controle);

                    # Destino de setor fora da FENORTE (tramita��o externa)
                    # se for origem externa n�o voltar� a origem externa
                    if ($statusProcesso <> 2)
                    {   
                        $controle = new Input('destinoExterno','texto','Setor Destino Fora da FENORTE:',1);
                        $controle->set_size(50);
                        $controle->set_linha(4);
                        $controle->set_title('Setor Destino de Fora da Fenorte');
                        $form->add_item($controle);
                    }

                    # submit
                    $controle = new Input('submit','submit');
                    $controle->set_valor(' Cadastrar ');
                    $controle->set_size(20);
                    $controle->set_linha(1);
                    $controle->set_tabIndex(6);
                    $controle->set_accessKey('E');
                    $form->add_item($controle);

                $form->show();		

            $Window->fecha();
            break;

        #####################################

        case "editaProcesso" :
            /**
            * Fase que edita e grava a altera��o do assunto do processo
            */

            $formProcesso->gravar($processo);
            loadPage('?');

            break;

        #####################################

        case "inserirTramitacao" :
            /**
            * Fase que valida e insere uma tramita��o do processo
            */

            # Vari�veis para tratamento de erros
            $erro = 0;	  	// flag de erro: 1 - tem erro; 0 - n�o tem	
            $msgErro = null; 	// reposit�rio de mensagens de erro

            # Pega os valores digitados
            $numProcesso= post('processo');
            $data = post('data');
            $origem = post('origem');
            $origemExterna = post('origemExterna');
            $destino = post('destino');
            $destinoExterno = post('destinoExterno');
            $numTramitacoes = $intra->get_tramitacoes($numProcesso);

            # Instancia um objeto de valida��o
            $valida = new Valida();

            # verifica se a data foi preenchida
            if ($valida->vazio($data))
            {
                $msgErro.='O campo data deve ser preenchido !!\n';
                $erro = 1;
            }

            # verifica se a origem foi preenchida somente se n�o for inicial
            if($numTramitacoes <> 0) 
            {
                if (($valida->vazio($origem)) and ($valida->vazio($origemExterna)))
                {
                    $msgErro.='O campo de origem da tramita��o deve ser preenchido !!\n';
                    $erro = 1;
                }
            }

            # verifica se o destino foi preenchida
            if (($valida->vazio($destino)) and ($valida->vazio($destinoExterno)))
            {
                $msgErro.='O campo destino da tramita��o deve ser preenchido !!\n';
                $erro = 1;
            }

            # verifica se a origem e destino s�o iguais
            if ($origem == $destino)
            {
                $msgErro.='O processo n�o pode entrar e sair de um mesmo setor!!!\n';
                $erro = 1;
            }

            # formata a data se for controle do html5
            if(HTML5)
                $data = date_to_php($data);
            
            # verifica a validade da data
            if (!Data::validaData($data))
            {
                $msgErro.='A data n�o � v�lida !!\n';
                $erro = 1;
            }        

            if ($erro == 1)
            {
                $alert = new Alert($msgErro) ;
                $alert->show();

                back(1);
            }
            else
            {   
                # prepara os dados a serem gravados
                # passa a data para formato do banco de dados
                $data = date_to_bd($data);

                # pega o valor de origem            
                if($origem == "")
                    $origem = $origemExterna;

                # pega o valor de destino           
                if($destino == "")
                    $destino = $destinoExterno;            

                # Faz a grava��o no banco de dados
                $intra->set_tabela('tbprocessoMovimento');
                $intra->gravar(array("processo","data","origem","destino"), 
                               array($numProcesso,$data,$origem,$destino));

                # Pega o id
                $id = $intra->get_lastId();

                # Log
                $Objetolog = new Intra();
                $data = date("Y-m-d H:i:s");
                $Objetolog->registraLog($matricula,$data,'Tramitou o Processo '.$intra->get_numeroProcesso($numProcesso).'.','tbprocessoMovimento',$id);	

                loadPage('?');
            } 
            break;   

        ###############################	

        case "excluirTramitacao" :

            # verifica se o processo tem tramita��o
            $id = get('id');

             # Conecta com o banco de dados
             $intra = new $intra();
             $intra->set_tabela('tbprocessoMovimento');	# a tabela
             $intra->set_idCampo('idprocessoMovimento');	# o nome do campo id

            if($intra->excluir($id));
            {		        
                $Objetolog = new Intra();
                $data = date("Y-m-d H:i:s");
                $Objetolog->registraLog($matricula,$data,'Excluiu tramita��o do processo '.$intra->get_numeroProcesso($processo).'.','tbprocessoMovimento',$id);
            }

            loadPage('?');

            break;

        ###############################	

        case "excluirProcesso" :

            # pega o id (se tiver)
            $numTramitacao = $intra->get_tramitacoes($processo);
            $numProcesso = $intra->get_numeroProcesso($processo);
            if($numTramitacao == 0)
            {   
                # Conecta com o banco de dados
                $intra = new $intra();
                $intra->set_tabela('tbprocesso');	# a tabela
                $intra->set_idCampo('idprocesso');	# o nome do campo id


                if($intra->excluir($processo));
                {		        
                    $Objetolog = new Intra();
                    $data = date("Y-m-d H:i:s");
                    $Objetolog->registraLog($matricula,$data,'Excluiu o processo '.$numProcesso.'.','tbprocesso',$processo);

                    loadPage('areaServidor.php');
                }
            }
            else
            {
                $alert = new Alert('Voc� s� pode excluir processos que n�o tem tramita��o.');
                $alert->show();

                back(1);
            }

            break;

        #####################################
    }


    $page->terminaPagina();
}
?>