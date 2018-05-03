<?php
/**
 * Cadastro de Computador
 *  
 * By Alat
 */

# Servidor logado 
$idUsuario = NULL;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario,1);

if($acesso){    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();
	
    # Verifica a fase do programa
    $fase = get('fase','listar');

    # pega o id se tiver)
    $id = soNumeros(get('id'));

    # Pega o parametro de pesquisa (se tiver)
    if (is_null(post('parametro'))){                                # Se o parametro não vier por post (for nulo)
        $parametro = retiraAspas(get_session('sessionParametro'));  # passa o parametro da session para a variavel parametro retirando as aspas
    }else{ 
        $parametro = post('parametro');                             # Se vier por post, retira as aspas e passa para a variavel parametro			
        set_session('sessionParametro',$parametro);                 # transfere para a session para poder recuperá-lo depois
    }

    # Ordem da tabela
    $orderCampo = get('orderCampo');
    $orderTipo = get('order_tipo');

    # Começa uma nova página
    $page = new Page();			
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();

    # Abre um novo objeto Modelo
    $objeto = new Modelo();

    ################################################################

    # Nome do Modelo (aparecerá nos fildset e no caption da tabela)
    $objeto->set_nome('Cadastro de Processos');

    # botão de voltar da lista
    $objeto->set_voltarLista('areaServidor.php');

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar:');
    $objeto->set_parametroValue($parametro);

    # ordenação
    if(is_null($orderCampo)){
            $orderCampo = 1;
    }

    if(is_null($orderTipo)){
            $orderTipo = 'asc';
    }

    # select da lista
    $objeto->set_selectLista('SELECT data,
                                     numero,
                                     assunto,
                                     idProcesso,
                                     idProcesso
                                FROM tbprocesso
                               WHERE data LIKE "%'.$parametro.'%"
                                  OR numero LIKE "%'.$parametro.'%"	
                                  OR assunto LIKE "%'.$parametro.'%"		    
                            ORDER BY '.$orderCampo.' '.$orderTipo);	

    # select do edita
    $objeto->set_selectEdita('SELECT data,
                                     numero,
                                     assunto							    
                                FROM tbprocesso
                               WHERE idProcesso = '.$id);

    # ordem da lista
    $objeto->set_orderCampo($orderCampo);
    $objeto->set_orderTipo($orderTipo);
    $objeto->set_orderChamador('?fase=listar');

    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');
    $objeto->set_linkExcluir('?fase=excluir');
    
    # Retira os botões de editar e excluir padrões
    $objeto->set_botaoExcluir(FALSE);
    $objeto->set_botaoEditar(FALSE);

    # Parametros da tabela
    $objeto->set_label(array("Data","Número","Assunto","Movimentação"));
    $objeto->set_width(array(15,15,60));		
    $objeto->set_align(array("center","center","left"));
    $objeto->set_funcao(array("date_to_php"));
    
    # Botão de exibição dos servidores com permissão a essa regra
    $botao = new BotaoGrafico();
    $botao->set_label('');
    $botao->set_title('Movimentação do processo');
    $botao->set_url('?fase=movimentacao&id='.$id);
    $botao->set_image(PASTA_FIGURAS.'movimentacao.png',20,20);

    # Coloca o objeto link na tabela			
    $objeto->set_link(array("","","",$botao));

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbprocesso');

    # Nome do campo id
    $objeto->set_idCampo('idProcesso');

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);
    
    # Campos para o formulario
    $objeto->set_campos(array( 
                        array ( 'nome' => 'data',
                                'label' => 'data:',
                                'tipo' => 'data',
                                'size' => 20,
                                'title' => 'data do processo',
                                'required' => TRUE,
                                'autofocus' => TRUE,
                                'col' => 3,
                                'linha' => 1),
                        array ( 'linha' => 1,
                                'nome' => 'numero',
                                'label' => 'Processo:',
                                'tipo' => 'texto',
                                'title' => 'O numero do Processo',
                                'required' => TRUE,
                                'col' => 3,
                                'size' => 15),
                        array ( 'nome' => 'assunto',
                                'label' => 'Assunto:',
                                'tipo' => 'textarea',
                                'size' => array(90,5),
                                'title' => 'Assunto.',
                                'col' => 12,
                                'linha' => 2)	 	 	 	 	 	 
                    ));

    # Log
    $objeto->set_idUsuario($idUsuario);
    
    ################################################################
    switch ($fase)
    {
        case "" :
        case "listar" :
            $objeto->listar();
            break;

        case "editar" :	
        case "excluir" :	
        case "gravar" :		
            $objeto->$fase($id);		
            break;
        
        case "movimentacao" :
            # Limita o tamanho da tela
            $grid = new Grid();
            $grid->abreColuna(3);
            
            # Cria um menu
            $menu1 = new MenuBar();

            # Sair da Área do Servidor
            $linkVoltar = new Link("Voltar","?");
            $linkVoltar->set_class('button');
            $linkVoltar->set_title('Voltar');
            $menu1->add_link($linkVoltar,"left");

            $menu1->show();
            Gprocessos::exibeProcesso($id);
            
            $grid->fechaColuna();
            $grid->abreColuna(9);
            $grid->fechaColuna();
            $grid->fechaGrid();
            break;
    }									 	 		

    $page->terminaPagina();
}else{
    loadPage("login.php");
}