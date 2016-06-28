<?php
/**
 * Gerencia as Variáveis do Sistema
 *  
 * By Alat
 */

# Reservado para o servidor logado
$idusuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idusuario,1);

if($acesso)
{    
    # Conecta ao Banco de Dados
    $intra = new Intra();
    $servidor = new Pessoal();
	
    # Verifica a fase do programa
    $fase = get('fase','listar');

    # pega o id se tiver)
    $id = soNumeros(get('id'));

    # Pega o parametro de pesquisa (se tiver)
    if (is_null(post('parametro')))									# Se o parametro não vier por post (for nulo)
        $parametro = retiraAspas(get_session('sessionParametro'));	# passa o parametro da session para a variavel parametro retirando as aspas
    else
    { 
        $parametro = post('parametro');								# Se vier por post, retira as aspas e passa para a variavel parametro			
        set_session('sessionParametro',$parametro);			 		# transfere para a session para poder recuperá-lo depois
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
    $objeto->set_nome('Configurações do Sistema');

    # botão salvar
    $objeto->set_botaoSalvarGrafico(false);

    # botão de voltar da lista
    $objeto->set_voltarLista('administracao.php');

    # controle de pesquisa
    $objeto->set_parametroLabel('Pesquisar nos campos Nome e/ou Comentário:');
    $objeto->set_parametroValue($parametro);

    # ordenação
    if(is_null($orderCampo))
            $orderCampo = 1;

    if(is_null($orderTipo))
            $orderTipo = 'asc';

    # select da lista
    $objeto->set_selectLista('SELECT nome,
                                     valor,
                                     comentario,
                                     idVariaveis
                                FROM tbvariaveis	
                               WHERE nome LIKE "%'.$parametro.'%"
                                  OR comentario LIKE "%'.$parametro.'%"		   									   
                            ORDER BY '.$orderCampo.' '.$orderTipo);	

    # select do edita
    $objeto->set_selectEdita('SELECT nome,
                                     comentario,	
                                     valor							    
                                FROM tbvariaveis
                               WHERE idVariaveis = '.$id);

    # ordem da lista
    $objeto->set_orderCampo($orderCampo);
    $objeto->set_orderTipo($orderTipo);
    $objeto->set_orderChamador('?fase=listar');

    # Caminhos
    $objeto->set_linkEditar('?fase=editar');
    $objeto->set_linkGravar('?fase=gravar');
    $objeto->set_linkListar('?fase=listar');
    $objeto->set_linkExcluir('?fase=excluir');

    # Parametros da tabela
    $objeto->set_label(array("Nome","Valor","Comentário"));
    $objeto->set_width(array(15,10,65));		
    $objeto->set_align(array("left","center","left"));

    # Classe do banco de dados
    $objeto->set_classBd('Intra');

    # Nome da tabela
    $objeto->set_tabela('tbvariaveis');

    # Nome do campo id
    $objeto->set_idCampo('idVariaveis');

    # Tipo de label do formulário
    $objeto->set_formlabelTipo(1);

    # Campos para o formulario
    $objeto->set_campos(array( 
                        array ( 'nome' => 'nome',
                                'label' => 'Nome:',
                                'tipo' => 'texto',
                                'size' => 90,
                                'title' => 'Nome da Variável.',
                                'required' => true,
                                'col' => 12,
                                'linha' => 1),					
                        array ( 'nome' => 'comentario',
                                'label' => 'Comentário:',
                                'tipo' => 'textarea',
                                'size' => array(90,5),
                                'required' => true,
                                'title' => 'Descrição resumida da utilidade da variável.',
                                'col' => 12,
                                'linha' => 2),
                        array ( 'nome' => 'valor',
                                'label' => 'Valor:',
                                'tipo' => 'texto',
                                'size' => 90,
                                'title' => 'Valor da Variável.',
                                'required' => true,
                                'col' => 12,
                                'linha' => 3),	 	 	 	 	 	 
                    ));

    # Log
    $objeto->set_idusuario($idusuario);
    
    ################################################################
    switch ($fase)
    {
        case "" :
        case "listar" :
            # Rotina de listar
            $divListar = new Div('divListar');
            $divListar->abre();
                    $objeto->listar();
            $divListar->fecha();
            break;

        case "editar" :	
        case "excluir" :	
        case "gravar" :		
            $objeto->$fase($id);		
            break;		
    }									 	 		

    $page->terminaPagina();
}
?>