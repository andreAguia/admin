<?php

/**
 * documentaFuncao
 * 
 * Gera documentação de uma função
 */
# Servidor logado 
$idUsuario = null;

# Configuração
include ("_config.php");

# Permissão de Acesso
$acesso = Verifica::acesso($idUsuario, 1);

if ($acesso) {

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho
    AreaServidor::cabecalho();

    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);

    # Pega a função a ser documentada
    $fase = get('fase');
    $sistema = get('sistema');      // Informa a pasta a ser lido
    $funcao = get('funcao');        // Método a ser exibido, se for "" exibe os dados da classe, se for "codigo" exibe o código

    switch ($sistema) {
        case "Framework" :
            $pasta = PASTA_CLASSES_GERAIS;
            break;

        case "Grh" :
            $pasta = PASTA_CLASSES_GRH;
            break;

        case "areaServidor" :
            $pasta = PASTA_CLASSES;
            break;
    }



    # Cria um menu
    $menu = new MenuBar();

    # Botão voltar
    if ($funcao == "codigo") {
        # Se estiver exibindo o código o voltar volta para a classe
        $linkBotao1 = new Link("Voltar", '?sistema=' . $sistema . '&funcao=' . $funcao);
        $linkBotao1->set_class('button');
        $linkBotao1->set_title('Volta para a página anterior');
        $linkBotao1->set_accessKey('V');
        $menu->add_link($linkBotao1, "left");
    } else {
        # Se estiver exibindo a classe o voltar volta para o menu de classes
        $linkBotao1 = new Link("Voltar", 'documentacao.php?fase=sistema&sistema=' . $sistema);
        $linkBotao1->set_class('button');
        $linkBotao1->set_title('Volta para a página anterior');
        $linkBotao1->set_accessKey('V');
        $menu->add_link($linkBotao1, "left");
    }

    # Botão codigo
    if ($funcao <> "codigo") {
        $linkBotao2 = new Link("Exibe o Código", "?sistema=$sistema&funcao=codigo");
        $linkBotao2->set_class('button');
        $linkBotao2->set_title('Exibe o código fonte');
        $linkBotao2->set_accessKey('C');
        $menu->add_link($linkBotao2, "right");
    }

    $menu->show();


    # Exibe a função
    if (!is_null($funcao)) {

        # Divide a tela
        $grid2 = new Grid();
        $grid2->abreColuna(4, 3);

        # Coluna do nome da função
        $callout = new Callout();
        $callout->abre();

        # Inicia a documentação
        $doc = new DocumentaFuncao(PASTA_FUNCOES_GERAIS . "/funcoes.gerais.php");

        # Pega os dados da funcao
        $nomeFuncao = $doc->get_nomeMetodo();
        $descricaoFuncao = $doc->get_descricaoMetodo();
        $deprecatedFuncao = $doc->get_deprecatedMetodo();
        $syntaxFuncao = $doc->get_syntaxMetodo();
        $retornoFuncao = $doc->get_retornoMetodo();
        $notaFuncao = $doc->get_notaMetodo();
        $parametrosFuncao = $doc->get_parametrosMetodo();
        $exemploFuncao = $doc->get_exemploMetodo();
        $categoriaFuncao = $doc->get_categoriaMetodo();
        $autorFuncao = $doc->get_autorMetodo();

        # Busca a função dentro do array
        $keyFuncao = array_search($funcao, $nomeFuncao);

        # Função
        echo '<h4>' . $nomeFuncao[$keyFuncao] . '</h4>';

        $callout->fecha();
        $grid2->fechaColuna();

        # Coluna da documentação detalhada
        $grid2->abreColuna(8, 9);
        $callout = new Callout("success");
        $callout->abre();

        switch ($fase) {
            case "" :
                # Nome
                echo '<h5>' . $nomeFuncao[$keyFuncao] . '</h5>';

                # Decrição
                echo $descricaoFuncao[$keyFuncao];
                br();

                # Deprecated        
                if ((isset($deprecatedFuncao[$keyFuncao])) AND ($deprecatedFuncao[$keyFuncao])) {
                    br(2);
                    echo '<div class="callout alert">';
                    echo '<h6>DEPRECATED</h6> Esta Função deverá ser descontiuado nas próximas versões.<br/>Seu uso é desaconselhado.';
                    echo '</div>';
                }

                # Categoria
                if (isset($categoriaFuncao[$keyFuncao])) {
                    p("Categoria: $categoriaFuncao[$keyFuncao]", 'right', 'f12');
                }

                # Autor
                if (isset($autorFuncao[$keyFuncao])) {
                    echo '<small>Autor: ' . $autorFuncao[$keyFuncao] . '</small>';
                }

                hr();

                # Syntax
                if (isset($syntaxFuncao[$keyFuncao])) {
                    echo 'Sintaxe:';
                    echo '<pre>' . $syntaxFuncao[$keyFuncao] . '</pre>';
                    p('Parâmetros entre [ ] são opcionais.', 'right', 'f10');
                }

                # Return
                if (isset($retornoFuncao[$keyFuncao])) {
                    echo 'Valor Retornado:';
                    echo '<div class="callout secondary">';
                    echo $retornoFuncao[$keyFuncao];
                    echo '</div>';
                }

                # Nota
                if (isset($notaFuncao[$keyFuncao])) {
                    # Vê quantas notas existem
                    $qtdadeNota = count($notaFuncao[$keyFuncao]);

                    # Percorre as notas
                    for ($i = 0; $i < $qtdadeNota; $i++) {

                        echo 'Nota: ';

                        if ($qtdadeNota > 1) {
                            echo ($i + 1);
                        }

                        # Exibe a nota
                        $callout = new Callout("warning");
                        $callout->abre();
                        echo $notaFuncao[$keyFuncao][$i];
                        $callout->fecha();
                    }
                }

                # Parâmetros
                if (isset($parametrosFuncao[$keyFuncao])) {
                    echo 'Parâmetros:';

                    $tabela = new Tabela();
                    #array_shift($lista);     
                    $tabela->set_conteudo($parametrosFuncao[$keyFuncao]);
                    $tabela->set_label(array('Nome', 'Tipo', 'Padrão', 'Descrição'));
                    $tabela->set_align(array("center", "center", "center", "left"));
                    $tabela->set_width(array(10, 10, 10, 60));
                    $tabela->show();
                }

                # Exemplo
                if (isset($exemploFuncao[$keyFuncao])) {

                    # Define o arquivo de exemplo
                    $arquivoExemplo = PASTA_FUNCOES_GERAIS . "exemplos/" . rtrim($exemploFuncao[$keyFuncao]);

                    # Verifica se o arquivo existe
                    if (file_exists($arquivoExemplo)) {

                        echo 'Exemplo:';
                        echo '<pre>';
                        $linesExample = file($arquivoExemplo);

                        # Percorre o arquivo e guarda os dados em um array
                        foreach ($linesExample as $linha) {
                            $linha = htmlspecialchars($linha);
                            echo $linha;
                        }
                        echo '</pre>';
                        br();

                        # Roda o exemplo
                        echo 'O exemplo acima exibirá o seguinte resultado:';

                        # Cria borda para o exemplo
                        $calloutExemplo = new Callout();
                        $calloutExemplo->abre();

                        include PASTA_FUNCOES_GERAIS . "exemplos/" . rtrim($exemploFuncao[$keyFuncao]);

                        $calloutExemplo->fecha();
                    } else {
                        echo 'Exemplo:';
                        $callout1 = new Callout();
                        $callout1->abre();
                        echo "Arquivo de exemplo não encontrado";
                        $callout1->fecha();
                    }
                }
                break;

            case "codigo" :
                echo '<pre>';

                # Define o arquivo da classe
                $arquivoExemplo = PASTA_FUNCOES_GERAIS . "/funcoes.gerais.php";

                # Exibe o nome do arquivo
                echo str_repeat("#", 80);
                br();
                echo '# Arquivo:' . $arquivoExemplo;
                br();
                echo str_repeat("#", 80);
                br(2);

                # Marca se é a função desejada
                $funcaoDesejada = false;

                # variável que conta o número da linha
                $numLinha = 1;

                # Verifica a existência do arquivo
                if (file_exists($arquivoExemplo)) {
                    $linesCodigo = file($arquivoExemplo);

                    # Percorre o arquivo e guarda os dados em um array
                    foreach ($linesCodigo as $linha) {
                        $linha = htmlspecialchars($linha);

                        if (stristr($linha, "function")) {
                            $posicao = stripos($linha, 'function');   // marca posição da palavra function
                            $posicaoFinal = stripos($linha, '(');     // marca posição final do nome do método
                            $tamanho = $posicaoFinal - $posicao - 9;     // define o tamanho 

                            $nome = substr($linha, $posicao + 9, $tamanho);   // extrai o nome
                            if ($nome == $nomeFuncao[$keyFuncao]) {
                                $funcaoDesejada = true;
                            } else {
                                $funcaoDesejada = false;
                            }
                        }

                        if ($funcaoDesejada) {
                            # Exibe o número da linha
                            echo "<span id='numLinhaCodigo'>" . formataNumLinha($numLinha) . "</span> ";

                            # Exibe o código
                            echo $linha;

                            # Incrementa o ~umero da linha
                            $numLinha++;
                        }
                    }
                } else {
                    echo "Arquivo de exemplo não encontrado";
                }

                echo '</pre>';
                break;
        }

        $callout->fecha();
        $grid2->fechaColuna();
        $grid2->fechaGrid();
    }

    $grid->fechaColuna();
    $grid->fechaGrid();

    $page->terminaPagina();
} else {
    loadPage("login.php");
}