<?php

/**
 * classe Tabela
 * 
 * Classe para criação de tabelas
 * 
 * By Alat
 */
 
 
class Tabela
{
    private $class;	          # class da Tabela
    private $id = null;           # id da tabela   
    private $titulo = null;       # String Título que aparecerá no alto da tabela 

    private $conteudo;	          # Array com o conteúdo da tabela principal
    private $label = null;        # Array com o cabeçalho de cada coluna
    private $align = null;        # Array com o alinhamento de cada coluna
    private $width = null;        # Array com o tamanho de cada coluna em %
	
    private $totalRegistro = true; # true ou false informa se terá ou não total de registros
    private $footTexto = null;     # Exibe uma mensagem no rodapé
    private $idCampo = null;
    
    # Link condicional
    private $link = null;                   # array de objetos link correspondente a coluna em que ele aparece
    private $linkCondicional = null;        # array com o valor que a coluna deve ter para ter o link
    private $linkCondicionalOperador = '='; # operador da comparação. pode ser (=,<>, < ou >)
    
    
    private $numeroOrdem = false;       # Exibe (qualdo true) uma numeração das colunas
    private $numeroOrdemTipo = 'c';     # Informa que a ordenação será 'c' crescente ou 'd' decrescente
	
    # Formatação condicional (exibe a tr com cor diferente dependendo de algum valor)
    private $formatacaoCondicional = null;     
    
    # Imagem Condicional (exibe um objeto imagem ao invés do valor dependendo de algum valor)
    private $imagemCondicional = null;        
    
    private $funcao = null;             # array de funções
    
    private $zebrado = true;            # se a tabela será zebrado
    
    # da Classe
    private $classe = null;             # array de classes
    private $metodo = null;             # array de metodo das classes
    
    # das rotinas de exclusão
    private $excluir = null;
    private $nomeColunaExcluir = 'Excluir';	    # Nome da Coluna
    private $excluirCondicional = null;
    private $excluirCondicao = null;
    private $excluirColuna = null;
    private $excluirBotao = 'bullet_cross.png';	# Figura do botão

    # das rotinas de edição
    private $editar = null;
    private $nomeColunaEditar = 'Editar';	# Nome da Coluna
    private $editarCondicional = null;
    private $editarCondicao = null;
    private $editarColuna = null;
    private $editarBotao = 'bullet_edit.png';	# Figura do botao
    
    # do form de check
    private $check = false;
    private $checkSubmit = null;

    # da ordenação
    private $orderCampo = null;
    private $orderTipo = null;
    private $orderChamador;

    # outros
    private $textoRessaltado = null;	# string que será ressaltada no resultado da tabela (usado para resaltar pesquisas)

    ###########################################################

    /**
     * método construtor
     *  
     * @param 	$nome	string	-> nome da classe da tabela para o css
     */
    public function __construct($id = null,$class = 'tabelaPadrao')
    {
        $this->class = $class;
        $this->id = $id;
    }

    ###########################################################

    /**
     * Métodos get e set construídos de forma automática pelo 
     * metodo mágico __call.
     * Esse método cria um set e um get para todas as propriedades da classe.
     * Se o método não estiver previsto no __call o php procura pela existência
     * do método na classe.
     * 
     * O formato dos métodos devem ser:
     * 	set_propriedade
     * 	get_propriedade
     * 
     * @param 	$metodo		O nome do metodo
     * @param 	$parametros	Os parâmetros inseridos  
     */
    public function __call ($metodo, $parametros)
    {
        ## Se for set, atribui um valor para a propriedade
        if (substr($metodo, 0, 3) == 'set')
        {
          $var = substr($metodo, 4);
          $this->$var = $parametros[0];
        }

        # Se for Get, retorna o valor da propriedade
        #if (substr($metodo, 0, 3) == 'get')
        #{
        #  $var = substr(strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $metodo)), 4);
        #  return $this->$var;
        #}
    }
    ###########################################################

    /**
     * Método set_cabecalho
     * 
     * @param 	$label	array	-> array com o título de cada coluna
     * @param 	$width	array	-> array com o tamanho de cada coluna em %
     * @param 	$align	array	-> array com o alinhamento da coluna pode ser center, left, right ou justify
     */
    public function set_cabecalho($label = null,$width = null,$align = null)
    {
        $this->label = $label;
        $this->width = $width;
        $this->align = $align;
    }

    ###########################################################

    /**
     * M�todo set_excluirCondicional
     * 
     * Define uma condi��o para exibir ou n�o a op��o de exclus�o
     * Usado na rotina de f�rias para colocar a op��o de exclus�o 
     * somente nas f�rias com status de solicitada.
     * 
     * @param 	$excluirCondicional string -> url para a rotina de exclus�o
     * @param 	$excluirCondicao	 string -> valor que exibe o bot�o de exclus�o
     * @param 	$excluirColuna		 integer -> n�mero da coluna cujo valor ser� comparado
     */
    public function set_excluirCondicional($excluirCondicional,$excluirCondicao,$excluirColuna)
    {
        $this->excluirCondicional = $excluirCondicional;
        $this->excluirCondicao = $excluirCondicao;
        $this->excluirColuna = $excluirColuna;
    }

    ###########################################################

    /**
     * M�todo set_editarCondicional
     * 
     * Define uma condi��o para exibir ou n�o a op��o de edi��o
     * Usado na rotina de servi�o para exibir a edi��o aos usu�rios 
     * comuns somente das OS desse mesmo usu�rio.
     * 
     * @param 	$editarCondicional string -> url para a rotina de editar
     * @param 	$editarCondicao	 string -> valor que exibe o bot�o de editar
     * @param 	$editarColuna		 integer -> n�mero da coluna cujo valor ser� comparado
     */
    public function set_editarCondicional($editarCondicional,$editarCondicao,$editarColuna)
    {
        $this->editarCondicional = $editarCondicional;
        $this->editarCondicao = $editarCondicao;
        $this->editarColuna = $editarColuna;
    }
    
    ###########################################################

    /**
     * M�todo set_order
     * 
     * @param 	$orderCampo 	integer -> coluna da tabela onde ser� ordenado 
     * @param 	$orderTipo		string -> pode ser 'asc' ou 'desc'. informa o tipo de ordena��o
     * @param 	$orderChamador	string -> 
     */
    public function set_order($orderCampo,$orderTipo,$orderChamador)
    {
        $this->orderCampo = $orderCampo;
        $this->orderTipo = $orderTipo;
        $this->orderChamador = $orderChamador;
    }

    ###########################################################

    public function show()
    {
        $zebra = 1;                             // contador do efeito zebrado
        $numRegistros = 0;				        // Contador de registros
        $numColunas = count($this->label);		// Calcula o número de colunas da tabela
        $numColunasOriginal = $numColunas;		// O número de colunas da tabela sem o edit, exclui, etc
        
        $colunaEdita = 999;
        $colunaExcluir = 999;
        $colunaExcluirCondicional = 999;
        $colunaEditarCondicional = 999;

        # Quando existir rotina de editar
        # acrescenta colunas extras e calcula a posi��o na tabela
        if($this->editar <> null)
        {
            $colunaEdita = $numColunas;
            $numColunas++;
            $this->label[$colunaEdita] = 'Rotina de edição de registro';
        }

        # Quando existir rotina de excluir
        # acrescenta colunas extras e calcula a posição na tabela
        if($this->excluir <> null)
        {
            $colunaExcluir = $numColunas;
            $numColunas++;
            $this->label[$colunaExcluir] = 'Rotina de exclusão de registro';
        }	

        # Quando existir rotina de excluir condicional
        # acrescenta colunas extras e calcula a posição na tabela
        if($this->excluirCondicional <> null)
        {
            $colunaExcluirCondicional = $numColunas;
            $numColunas++;   
            $this->label[$colunaExcluirCondicional] = 'Rotina de exclusão de registro';
        }

        if($this->editarCondicional <> null)
        {
            $colunaEditarCondicional = $numColunas;
            $numColunas++;  
            $this->label[$colunaEditarCondicional] = 'Rotina de Edição de registro';
        }
               
        # Início da Tabela
        echo '<table class="'.$this->class.'"';        
        
        # id
        if(!is_null($this->id))
            echo ' id="'.$this->id.'"';        
              
        echo '>';
        
        # Colunas
        if($this->numeroOrdem)
            echo '<col style="width:5%">';

        for($a = 0;$a < $numColunas;$a += 1)
        {
            if(isset($this->width[$a]))
                echo '<col style="width:'.$this->width[$a].'%">';
        }

        # Cabeçalho
        echo '<thead>';
        
        # título
        if ($this->titulo)
            echo '<caption>'.$this->titulo.'</caption>';

        # ordem ascendente ou descendente?
        if($this->orderTipo == "asc")
            $this->orderTipo = "desc";
        else
            $this->orderTipo = "asc";

        # cabeçalho das colunas
        echo '<tr>';
                        
        # Reserva uma coluna para o número de ordem (se tiver)
        if($this->numeroOrdem)
            echo '<th title="Número de Ordem" id="numeroOrdem">#</th>';

        for($a = 0;$a < $numColunas;$a += 1)
        {        
            echo '<th title="'.$this->label[$a].'">';

            # colunas
            if(($this->editar <> null) and ($a == $colunaEdita))			// coluna de editar
                echo $this->nomeColunaEditar.'</th>';
            elseif(($this->excluir <> null) and ($a == $colunaExcluir))	// coluna de excluir
                echo $this->nomeColunaExcluir.'</th>';
            elseif(($this->excluirCondicional <> null) and ($a == $colunaExcluirCondicional))	// coluna de excluir_condicional
                echo $this->nomeColunaExcluir.'</th>';
            elseif(($this->editarCondicional <> null) and ($a == $colunaEditarCondicional))	// coluna de excluir_condicional
                echo $this->nomeColunaEditar.'</th>';	
            elseif($this->orderCampo <> null)							// coloca um link no cabe�alho da coluna para ordenamento (quando tiver ordenamento)
            {	
                $link = new Link($this->label[$a],$this->orderChamador.'&orderCampo='.($a+1).'&orderTipo='.$this->orderTipo);
                $link->show();
            }
            else
                echo $this->label[$a].'</th>';
        } // for

        echo '</tr>';
        echo '</thead>';

        # Corpo da Tabela
        echo '<tbody>';
        
        if($this->numeroOrdemTipo == 'c')
            $numOrdem = 1;  # Inicia o número de ordem quando tiver
        else
            $numOrdem = count($this->conteudo);  # Inicia o número de ordem quando tiver
        
        # Marca se a tr tem condicional para saber se poe ou não o zebrado
        $temCondicional = FAlSE;
            
        foreach ($this->conteudo as $row)
        {
            echo '<tr ';  
            
            # Formatação condicional
            if (!is_null($this->formatacaoCondicional))
            {
                #$this->zebrado = false; // tira o zebrado quando se tem formatação condicional
                $rowCondicional = $row;
                for ($a = 0;$a < ($numColunasOriginal);$a ++)
                {
                    foreach ($this->formatacaoCondicional as $condicional)
                    {
                        if($a == $condicional['coluna'])
                        {
                            # somente para nivel de comparação
                            # Coloca a função (se tiver)
                            if((isset($this->funcao[$a])) and ($this->funcao[$a] <> null)) 			
                            {
                                $nomedafuncao = $this->funcao[$a];
                                $rowCondicional[$a] = $nomedafuncao($row[$a]);
                            }
                            
                            # Coloca a classe (se tiver)
                            if((isset($this->classe[$a])) and ($this->classe[$a] <> null)) 			
                            {
                                $instancia = new $this->classe[$a]();
                                $metodoClasse = $this->metodo[$a];
                                $rowCondicional[$a] = $instancia->$metodoClasse($row[$a]);
                            }
                            
                            switch ($condicional['operador'])
                            {
                                case '=':
                                case '==':
                                    if($rowCondicional[$a] == $condicional['valor']){
                                        echo 'id="'.$condicional['id'].'"';
                                        $temCondicional = TRUE;
                                    }
                                    break;

                                case '<>':	
                                    if($rowCondicional[$a] <> $condicional['valor']){
                                        echo 'id="'.$condicional['id'].'"';
                                        $temCondicional = TRUE;
                                    }
                                    break;

                                case '>':	
                                    if($rowCondicional[$a] > $condicional['valor']){
                                        echo 'id="'.$condicional['id'].'"';
                                        $temCondicional = TRUE;
                                    }
                                    break;

                                case '<':	
                                    if($rowCondicional[$a] < $condicional['valor']){
                                        echo 'id="'.$condicional['id'].'"';
                                        $temCondicional = TRUE;
                                    }
                                    break;

                                case '>=':	
                                    if($rowCondicional[$a] >= $condicional['valor']){
                                        echo 'id="'.$condicional['id'].'"';
                                        $temCondicional = TRUE;
                                    }
                                    break;

                                case '<=':	
                                    if($rowCondicional[$a] <= $condicional['valor']){
                                        echo 'id="'.$condicional['id'].'"';
                                        $temCondicional = TRUE;
                                    }
                                    break;
                                default :
                                    $temCondicional = FALSE;
                            }
                            
                        }		
                    }
                }
            }				
        
            echo '>';// tr
            
            if($this->numeroOrdem)
            {
                echo '<td id="center"';
                
                # zebrado
                if(!$temCondicional){
                    if (($this->zebrado) && ($zebra == 1)){
                        echo ' class="zebrado1"';
                    }
                    if (($this->zebrado) && ($zebra == 0)){
                        echo ' class="zebrado0"';
                    }
                }
                echo'>'.$numOrdem.'</td>';            
            }
            
            if($this->numeroOrdemTipo == 'c')
                $numOrdem++;    # incrementa o número de ordem
            else
                $numOrdem--;    # decrementa o número de ordem
            
            $numRegistros ++;

            # Pega o id do Banco de dados
            if(isset($this->idCampo))
                $id = $row["$this->idCampo"]; 

            # percorre as colunas 
            for ($a = 0;$a < ($numColunas);$a ++)
            {
                echo '<td';
                
                # alinhamento
                if((isset($this->align[$a])) and ($this->align[$a] <> null)) 
                    echo ' id="'.$this->align[$a].'"';
                else
                    echo ' id="center"';
                
                # zebrado (beta)
                if(!$temCondicional){
                if (($this->zebrado) && ($zebra == 1))
                    echo ' class="zebrado1"';
                if (($this->zebrado) && ($zebra == 0))
                    echo ' class="zebrado0"';
                }
                echo '>';
                
                # colunas
                # Botão editar
                if(($this->editar <> null) and ($a == $colunaEdita)){	
                    $botao = new BotaoGrafico();
                    $botao->set_url($this->editar.'&id='.$id);
                    $botao->set_image(PASTA_FIGURAS_GERAIS.$this->editarBotao,20,20);
                    $botao->set_title($this->nomeColunaEditar.': '.$row[0]);
                    $botao->show();                    
                }
                elseif(($this->excluir <> null) and ($a == $colunaExcluir))	// coluna de excluir
                {
                    $botao = new BotaoGrafico();
                    $botao->set_url($this->excluir.'&id='.$id);
                    $botao->set_image(PASTA_FIGURAS_GERAIS.$this->excluirBotao,20,20);
                    $botao->set_title($this->nomeColunaExcluir.': '.$row[0]);
                    $botao->set_confirma('Deseja mesmo excluir?');
                    $botao->show();
                    
                    #$link = new Link('Excluir',$this->excluir.'&id='.$id);
                    #$link->set_image(PASTA_FIGURAS_GERAIS.$this->excluirBotao);
                    #$link->set_title($this->nomeColunaExcluir.': '.$row[0]);
                    
                    #$link->show();
                }						
                elseif(($this->excluirCondicional <> null) and ($a == $colunaExcluirCondicional))	// coluna de excluir_condicional
                {
                    if($row[$this->excluirColuna] == $this->excluirCondicao)
                    {
                        $link = new Link('Excluir',$this->excluirCondicional.'&id='.$id);
                        $link->set_image(PASTA_FIGURAS_GERAIS.$this->excluirBotao);
                        $link->set_title('Exclui: '.$row[0]);
                        $link->set_confirma('Deseja mesmo excluir?');
                        $link->show();
                    }
                }
                elseif(($this->editarCondicional <> null) and ($a == $colunaEditarCondicional))	// coluna de editar_condicional
                {
                    if($row[$this->editarColuna] == $this->editarCondicao){
                        $link = new Link('Editar',$this->editarCondicional.'&id='.$id);
                        $link->set_image(PASTA_FIGURAS_GERAIS.$this->editarBotao);
                        $link->set_title($this->nomeColunaEditar.': '.$row[0]);
                        $link->show();
                    }
                }

                # Coloca a função (se tiver)
                if((isset($this->funcao[$a])) and ($this->funcao[$a] <> null)) 			
                {
                    $nomedafuncao = $this->funcao[$a];
                    $row[$a] = $nomedafuncao($row[$a]);
                }
                
                # Coloca a classe (se tiver)
                if((isset($this->classe[$a])) and ($this->classe[$a] <> null)) 			
                {
                    $instancia = new $this->classe[$a]();
                    $metodoClasse = $this->metodo[$a];
                    $row[$a] = $instancia->$metodoClasse($row[$a]);
                }
                
                # Coloca o link (se tiver)
                if((isset($this->linkCondicional[$a])) and ($this->linkCondicional[$a] <> null))
                {
                    if($this->linkCondicionalOperador == '=')
                    {
                        if($this->linkCondicional[$a] == $row[$a])
                        {
                            if((isset($this->link[$a])) and ($this->link[$a] <> null)) 
                                $this->link[$a]->show($id);
                        }
                    }
                    
                    if($this->linkCondicionalOperador == '<>')
                    {
                        if($this->linkCondicional[$a] <> $row[$a])
                        {
                            if((isset($this->link[$a])) and ($this->link[$a] <> null)) 
                                $this->link[$a]->show($id);
                        }
                    }
                }
                else
                {
                    if((isset($this->link[$a])) and ($this->link[$a] <> null)) 
                        $this->link[$a]->show($id);
                }
                
                # Se não é coluna de editar, nem de excluir, nem excluir condicional, nem de link etc
                if (($a <> $colunaEdita) and ($a <> $colunaExcluir) and ($a <> $colunaExcluirCondicional) and ($a <> $colunaEditarCondicional)and ((!isset($this->link[$a])) or ($this->link[$a] == null)))
                {   
                    # verifica se tem imagem condicional, se tiver exibe o gráfico ao invel do valor                
                    if (!is_null($this->imagemCondicional))
                    {
                        # pega as colunas que possuem imagens 
                        $colunasImagem = array();
                        foreach ($this->imagemCondicional as $condicionalColuna)
                        { 
                            array_push($colunasImagem,$condicionalColuna['coluna']);
                        }

                        $contadorRow = 0;   // evita que o texto seja escrito mais de uma vez

                        foreach ($this->imagemCondicional as $condicionalImagem)
                        { 
                            if($a == $condicionalImagem['coluna'])
                            {
                                switch ($condicionalImagem['operador'])
                                {
                                    case '=':
                                    case '==':
                                        if($row[$a] == $condicionalImagem['valor'])
                                            $condicionalImagem['imagem']->show();
                                        break;

                                    case '<>':	
                                        if($row[$a] <> $condicionalImagem['valor'])
                                            $condicionalImagem['imagem']->show();
                                        break;	

                                    case '>':	
                                        if($row[$a] > $condicionalImagem['valor'])
                                            $condicionalImagem['imagem']->show();
                                        break;

                                    case '<':	
                                        if($row[$a] < $condicionalImagem['valor'])
                                            $condicionalImagem['imagem']->show();
                                        break;

                                    case '>=':	
                                        if($row[$a] >= $condicionalImagem['valor'])
                                            $condicionalImagem['imagem']->show();
                                        break;                                   

                                    case '<=':	
                                        if($row[$a] <= $condicionalImagem['valor'])
                                            $condicionalImagem['imagem']->show();
                                        break;
                                }
                            }
                            else
                            {
                                if((!in_array($a,$colunasImagem)) and ($contadorRow == 0))
                                {
                                    if((!is_null($this->textoRessaltado)) AND ($this->textoRessaltado <> ""))
                                    {
                                        $row[$a] = get_bold($row[$a],$this->textoRessaltado);
                                        echo $row[$a];
                                    }
                                    else
                                        echo $row[$a];
                                    
                                    $contadorRow++;
                                }
                            }
                        }                            
                    }
                    elseif((!is_null($this->textoRessaltado)) AND ($this->textoRessaltado <> "")) # Verifica se tem negrito
                    {
                        $row[$a] = get_bold($row[$a],$this->textoRessaltado);
                        echo $row[$a];
                    }
                     else
                         echo $row[$a];
                }                
                echo '</td>';
            }
            # muda o zebrado
            if ($zebra == 1) 
                $zebra = 0;
            else
                $zebra = 1;
            
            echo '</tr>';
        }

        echo '</tbody>';
        
        # Rotapé da Tabela
        if (($this->totalRegistro) OR ($this->footTexto))
        {
            echo '<tfoot>';
            if ($this->numeroOrdem)
                echo '<tr><td colspan="'.($numColunas+1).'" title="Total de itens da tabela">';
            else    
                echo '<tr><td colspan="'.($numColunas).'" title="Total de itens da tabela">';

            if(is_null($this->footTexto))
                echo 'Total:   '.$numRegistros;
            else
                echo $this->footTexto;

            echo '</td></tr>';
        }
        
        echo '</tfoot>';
        echo '</table>';
    }
}
?>