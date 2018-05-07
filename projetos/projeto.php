<?php
/**
 * Gestão de Projetos
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("../sistema/_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,1);

if($acesso){    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();
    $projeto = new Projeto();

    # Verifica a fase do programa
    $fase = get('fase','ínicial');
    
    # Pega os ids quando se é necessário
    $idProjeto = get('idProjeto');
    $idTarefa = get('idTarefa');
    $idEtiqueta = get('idEtiqueta');
    $grupo = get('grupo');
    $hojeGet = get('hoje');
    
    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);
    
    # Cabeçalho
    AreaServidor::cabecalho();

    # Define o botão voltar de acordo com a rotina
    if($fase == 'ínicial'){
        botaoVoltar('../sistema/administracao.php');
    }else{
        botaoVoltar('?');
    }
    titulo("Sistema de Gestão de Projetos");
    br();
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(3);

    # Menu de Projetos
    Gprojetos::menuProjetosAtivos($idProjeto);
    
    # Menu Cronológico
    Gprojetos::menuCronologico($fase);
    
    # Menu de Etiquetas
    Gprojetos::menuEtiquetas($idEtiqueta);

    $grid->fechaColuna();
    
    switch ($fase){
        case "ínicial" :
            
            $grid->abreColuna(9);
            
            # Menu de Projetos
            Gprojetos::cartoesProjetosAtivos($grupo);  
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
            
        ###########################################################
            
        case "projeto" :            
            $grid->abreColuna(9);
            
            # Pega os dados do projeto pesquisado
            $projetoPesquisado = $projeto->get_dadosProjeto($idProjeto);
            
            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(6,8,9);
            
                # Exibe o nome e a descrição
                p($projetoPesquisado[1],'descricaoProjetoTitulo');
                p($projetoPesquisado[2],'descricaoProjeto');
                
            $grid->fechaColuna();
            $grid->abreColuna(6,4,3);
                
                # Menu
                $menu1 = new MenuBar();

                # Nova Tarefa
                $link = new Link("+",'?fase=tarefaNova&idProjeto='.$idProjeto);
                $link->set_class('button');
                $link->set_title('Nova tarefa');
                $menu1->add_link($link,"right");
                
                $menu1->show();
                
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            
            hr("projetosTarefas");
            br();
            
            # Exibe as tarefas pendentes com data
            $lista = new ListaTarefas("Tarefas Pendentes com Data");
            $lista->set_projeto($idProjeto);
            $lista->set_datado(TRUE);
            $lista->show();
            
            # Exibe as tarefas pendentes sem data
            $lista = new ListaTarefas("Tarefas Pendentes sem Data");
            $lista->set_projeto($idProjeto);
            $lista->set_datado(FALSE);
            $lista->show();
            
            # Exibe as tarefas completatadas
            $lista = new ListaTarefas("Tarefas Concluídas");
            $lista->set_projeto($idProjeto);
            $lista->set_datado(NULL);
            $lista->set_pendente(FALSE);
            $lista->show();
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
                 
        ###########################################################
            
        case "projetoEtiqueta" :
            
            $grid->abreColuna(9);
            
            # Pega os dados da etiqueta pesquisado
            $etiquetaPesquisada = $projeto->get_dadosEtiqueta($idEtiqueta);
            
            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(6,8,9);
            
                # Exibe o nome e a descrição
                p($etiquetaPesquisada[1],'descricaoProjetoTitulo');
                p("Tarefas com a etiqueta: ".$etiquetaPesquisada[1],'descricaoProjeto');
                
            $grid->fechaColuna();
            $grid->abreColuna(6,4,3);
                # Menu
                $menu1 = new MenuBar();

                # Nova Tarefa
                $link = new Link("Editar",'?fase=etiquetaNova&idEtiqueta='.$idEtiqueta);
                $link->set_class('button');
                $link->set_title('Editar Etiqueta');
                $menu1->add_link($link,"right");
                
                $menu1->show();
                
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            
            hr("projetosTarefas");
            br();
            
            # Exibe as tarefas pendentes com data
            $lista = new ListaTarefas("Tarefas Pendentes com Data");
            $lista->set_etiqueta($idEtiqueta);
            $lista->set_datado(TRUE);
            $lista->show();
            
            # Exibe as tarefas pendentes sem data
            $lista = new ListaTarefas("Tarefas Pendentes sem Data");
            $lista->set_etiqueta($idEtiqueta);
            $lista->set_datado(FALSE);
            $lista->show();
            
            # Exibe as tarefas completatadas
            $lista = new ListaTarefas("Tarefas Concluídas");
            $lista->set_etiqueta($idEtiqueta);
            $lista->set_datado(NULL);
            $lista->set_pendente(FALSE);
            $lista->show();
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
                 
        ###########################################################   
            
         case "projetoNovo" :
             
            $grid->abreColuna(9);
             
            # Verifica se é incluir ou editar
            if(!is_null($idProjeto)){
                # Pega os dados desse projeto
                $dados = $projeto->get_dadosProjeto($idProjeto);
                $titulo = "Editar Projeto";
            }else{
                $dados = array(NULL,NULL,NULL,NULL);
                $titulo = "Novo Projeto";
            }
             
            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(12);
                p($titulo,"f18");
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            hr("projetosTarefas");
            br();
            
            # Formulário
            $form = new Form('?fase=validaProjeto&idProjeto='.$idProjeto);        
                    
            # projeto
            $controle = new Input('projeto','texto','Nome do Projeto:',1);
            $controle->set_size(50);
            $controle->set_linha(1);
            $controle->set_required(TRUE);
            $controle->set_autofocus(TRUE);
            $controle->set_placeholder('Nome do rojeto');
            $controle->set_title('O nome do Projeto a ser criado');
            $controle->set_valor($dados[1]);
            $form->add_item($controle);
            
            # descrição
            $controle = new Input('descricao','textarea','Descrição:',1);
            $controle->set_size(array(80,5));
            $controle->set_linha(2);
            $controle->set_title('A descrição detalhda do projeto');
            $controle->set_placeholder('Descrição');
            $controle->set_valor($dados[2]);
            $form->add_item($controle);
            
            # grupo
            $controle = new Input('grupo','texto','Nome do agrupamento:',1);
            $controle->set_size(50);
            $controle->set_linha(3);
            $controle->set_col(6);
            $controle->set_placeholder('Grupo');
            $controle->set_title('O nome agrupamento do Projeto');
            $controle->set_valor($dados[3]);
            $form->add_item($controle);
            
            # cor
            $controle = new Input('cor','combo','Cor:',1);
            $controle->set_size(10);
            $controle->set_col(6);
            $controle->set_linha(3);
            $controle->set_title('A cor da etiqueta');
            $controle->set_placeholder('Cor');
            $controle->set_array(array("secondary","primary","success","warning","alert"));
            $controle->set_valor($dados[2]);
            $form->add_item($controle);
            
            # submit
            $controle = new Input('submit','submit');
            $controle->set_valor('Salvar');
            $controle->set_linha(4);
            $form->add_item($controle);
            
            $form->show();
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
                        
        ###########################################################
            
        case "validaProjeto" :
            
            # Recuperando os valores
            $projeto = post('projeto');
            $descricao = post('descricao');
            $grupo = post('grupo');
            $cor = post('cor');
            
            # Cria arrays para gravação
            $arrayNome = array("projeto","descricao","ativo","grupo","cor");
            $arrayValores = array($projeto,$descricao,1,$grupo,$cor);
            
            # Grava	
            $intra->gravar($arrayNome,$arrayValores,$idProjeto,"tbprojeto","idProjeto");
            loadPage("?");
            break;
        
        ###########################################################  
            
        case "tarefaNova" :
                        
            $grid->abreColuna(9);
            
            # Verifica se é incluir ou editar
            if(!is_null($idTarefa)){
                # Pega os dados dessa etiqueta
                $dados = $projeto->get_dadosTarefa($idTarefa);
                $titulo = "Editar Tarefa";
                $idEtiqueta = $dados[7];
                $idProjeto = $dados[8];
            }else{
                $dados = array(NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
                $titulo = "Nova Tarefa";
            }
            
            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(12);
                p($projeto->get_nomeProjeto($idProjeto)." - ".$titulo,"f18");
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            hr("projetosTarefas");
            br();
            
            # Pega os dados da combo projeto
            $select = 'SELECT idProjeto,
                              projeto
                         FROM tbprojeto
                     ORDER BY projeto';
            
            $comboProjeto = $intra->select($select);
            array_unshift($comboProjeto, array(NULL,NULL)); # Adiciona o valor de nulo
            
            # Pega os dados da combo etiqueta
            $selectetiqueta = 'SELECT idEtiqueta, 
                                      etiqueta
                                 FROM tbprojetoetiqueta
                             ORDER BY etiqueta';
            
            $comboEtiqueta = $intra->select($selectetiqueta);
            array_unshift($comboEtiqueta, array(NULL,NULL)); # Adiciona o valor de nulo
            
            # Formuário
            $form = new Form('?fase=validaTarefa&idTarefa='.$idTarefa.'&hoje='.$hojeGet);        
                    
            # tarefa
            $controle = new Input('tarefa','texto','Tarefa:',1);
            $controle->set_size(200);
            $controle->set_linha(1);
            $controle->set_required(TRUE);
            $controle->set_autofocus(TRUE);
            $controle->set_placeholder('Tarefa');
            $controle->set_title('A tarefa a ser executada');
            $controle->set_valor($dados[1]);
            $form->add_item($controle);
            
            # descrição
            $controle = new Input('descricao','textarea','Descrição:',1);
            $controle->set_size(array(80,10));
            $controle->set_linha(2);
            $controle->set_title('A descrição detalhda do tarefa');
            $controle->set_placeholder('Descrição da tarefa');
            $controle->set_valor($dados[2]);
            $form->add_item($controle);
            
            # dataInicial
            $controle = new Input('dataInicial','data','Data:',1);
            $controle->set_size(20);
            $controle->set_linha(3);
            $controle->set_col(6);
            $controle->set_title('A data inicial da tarefa');
            $controle->set_placeholder('A Data Inicial');
            $controle->set_valor($dados[4]);
            $form->add_item($controle);
            
            # dataFinal
            $controle = new Input('dataFinal','data','Data da Conclusão:',1);
            $controle->set_size(20);
            $controle->set_linha(3);
            $controle->set_col(6);
            $controle->set_title('A data da conclusão da tarefa');
            $controle->set_placeholder('A Data da conclusão');
            $controle->set_valor($dados[5]);
            $form->add_item($controle);
            
            # etiqueta
            $controle = new Input('idEtiqueta','combo','Etiqueta:',1);
            $controle->set_size(20);
            $controle->set_linha(4);
            $controle->set_col(6);
            $controle->set_placeholder('Etiqueta');
            $controle->set_title('Uma etiqueta para ajudar na busca');
            $controle->set_array($comboEtiqueta);
            $controle->set_valor($dados[7]);
            $form->add_item($controle);
            
            # idProjeto
            $controle = new Input('idProjeto','combo','Projeto:',1);
            $controle->set_size(20);
            $controle->set_linha(4);
            $controle->set_col(6);
            $controle->set_array($comboProjeto);
            if(is_null($idTarefa)){
                $controle->set_valor($idProjeto);
            }else{
                $controle->set_valor($dados[8]);
            }
            $form->add_item($controle);     
            
            # pendente
            $controle = new Input('pendente','hidden','',1);
            $controle->set_size(20);
            $controle->set_linha(6);
            $controle->set_valor($dados[6]);
            $form->add_item($controle);     
            
            # submit
            $controle = new Input('submit','submit');
            $controle->set_valor('Salvar');
            $controle->set_linha(7);
            $form->add_item($controle);
            
            $form->show();
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
                        
        ###########################################################
            
        case "validaTarefa" :
            
            # Recuperando os valores
            $tarefa = post('tarefa');
            $descricao = post('descricao');
            $dataInicial = post('dataInicial');
            $dataFinal = post('dataFinal');
            $idProjeto = post('idProjeto');
            $idEtiqueta = post('idEtiqueta');
            $pendente = post('pendente');
            
            # Força a tarefa pendente quando é inclusão
            if(is_null($idTarefa)){
                $pendente = 1;
            }
                      
            # Cria arrays para gravação
            $arrayNome = array("tarefa","descricao","dataInicial","dataFinal","idProjeto","pendente","idEtiqueta");
            $arrayValores = array($tarefa,$descricao,$dataInicial,$dataFinal,$idProjeto,$pendente,$idEtiqueta);
            
            # Grava	
            $intra->gravar($arrayNome,$arrayValores,$idTarefa,"tbprojetoTarefa","idTarefa");
            if($hojeGet){
                loadPage("?fase=hoje");
            }else{
                loadPage("?fase=projeto&idProjeto=".$idProjeto);
            }
            break;
        
        ###########################################################  
        
        case "mudaTarefa" :
            
            # Pega os dados da tarefa
            $valor = $projeto->get_dadosTarefa($idTarefa);
            
            # Verifica o valor de pendente
            if($valor[6] == 1){
                $pendente = 0;
            }else{
                $pendente = 1;
            }
            
            # Cria arrays para gravação
            $arrayNome = array("pendente");
            $arrayValores = array($pendente);
            
            # Grava	
            $intra->gravar($arrayNome,$arrayValores,$idTarefa,"tbprojetoTarefa","idTarefa");
            
            if($hojeGet){
                loadPage("?fase=hoje");
            }else{            
            
                if(is_null($idProjeto)){
                    loadPage("?fase=projetoEtiqueta&idEtiqueta=".$idEtiqueta);
                }

                if(is_null($idEtiqueta)){
                    loadPage("?fase=projeto&idProjeto=".$idProjeto);
                }
            }
            break;
            
        ###########################################################  
            
         case "etiquetaNova" :
             
            $grid->abreColuna(9);
             
            # Verifica se é incluir ou editar
            if(!is_null($idEtiqueta)){
                # Pega os dados dessa etiqueta
                $dados = $projeto->get_dadosEtiqueta($idEtiqueta);
                $titulo = "Editar Etiqueta";
            }else{
                $dados = array(NULL,NULL,NULL);
                $titulo = "Nova Etiqueta";
            } 
             
            # Titulo
            $grid = new Grid();
            $grid->abreColuna(12);
                p($titulo,"f18");
            $grid->fechaColuna(); 
            $grid->fechaGrid(); 
            hr("projetosTarefas");
            br();
            
            # Formulário
            $form = new Form('?fase=validaEtiqueta&idEtiqueta='.$idEtiqueta);        
                    
            # projeto
            $controle = new Input('etiqueta','texto','Etiqueta:',1);
            $controle->set_size(50);
            $controle->set_linha(1);
            $controle->set_col(4);
            $controle->set_required(TRUE);
            $controle->set_autofocus(TRUE);
            $controle->set_placeholder('Etiqueta');
            $controle->set_title('A Etiqueta');
            $controle->set_valor($dados[1]);
            $form->add_item($controle);
            
            # cor
            $controle = new Input('cor','combo','Cor:',1);
            $controle->set_size(10);
            $controle->set_col(3);
            $controle->set_linha(1);
            $controle->set_title('A cor da etiqueta');
            $controle->set_placeholder('Cor');
            $controle->set_array(array("secondary","primary","success","warning","alert"));
            $controle->set_valor($dados[2]);
            $form->add_item($controle);
            
            # submit
            $controle = new Input('submit','submit');
            $controle->set_valor('Salvar');
            $controle->set_linha(3);
            $form->add_item($controle);
            
            $form->show();
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
                        
        ###########################################################
            
        case "validaEtiqueta" :
            
            # Recuperando os valores
            $etiqueta = post('etiqueta');
            $cor = post('cor');
                      
            # Cria arrays para gravação
            $arrayNome = array("etiqueta","cor");
            $arrayValores = array($etiqueta,$cor);
            
            # Grava	
            $intra->gravar($arrayNome,$arrayValores,$idEtiqueta,"tbprojetoEtiqueta","idEtiqueta");
            
            if(is_null($idEtiqueta)){
                loadPage("?");
            }else{
                loadPage("?fase=projetoEtiqueta&idEtiqueta=".$idEtiqueta);
            }
            break;
        
        ###########################################################
            
        case "hoje" :
            
            $grid->abreColuna(9);
            
            # Nome do projeto
            $grid = new Grid();
            $grid->abreColuna(9);
            
            $hoje = date("d/m/Y");
            
                # Exibe o nome e a descrição
                p('Tarefas agendadas para hoje.','descricaoProjetoTitulo');
                p('Tarefas de todos os projetos agendadas para hoje - '.$hoje,'descricaoProjeto');
                
            $grid->fechaColuna();
            $grid->abreColuna(3);
            
                # Exibe o link de Nova Tarefa
                #$menu2 = new Menu();
                #$menu2->add_item('link','+ Nova Tarefa','?fase=tarefaNova&idProjeto='.$idProjeto);
                #$menu2->show();
                
            $grid->fechaColuna();
            $grid->fechaGrid(); 
            
            hr("projetosTarefas");
            br();
            
            # Exibe as tarefas pendentes com data
            $lista = new ListaTarefas();
            $lista->showPendenteAtrasada();
            $lista->showPendenteHoje();
            
            $grid->fechaColuna();
            $grid->fechaGrid();    
            break;
                 
        ###########################################################  
            
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();  
    
    $page->terminaPagina();
}else{
    loadPage("login.php");
}