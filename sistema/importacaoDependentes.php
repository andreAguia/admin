<?php
/**
 * Rotina de Importação de Faltas
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

    # Começa uma nova página
    $page = new Page();
    $page->iniciaPagina();

    # Cabeçalho da Página
    AreaServidor::cabecalho();
    
    # Verifica a fase do programa
    $fase = get('fase');
    
    # Parâmetros da importação
    $tt = 0;        // contador de registros
    $problemas = 0; // contador de problemas
    $contador = 0;  // contador de linhas
    
    # Limita o tamanho da tela
    $grid = new Grid();
    $grid->abreColuna(12);
    
    br();

    #########################################################################
    
    switch ($fase){       
        case "" : 
            # Cria menu
            $menu = new MenuBar();
            
            # Botão voltar
            $link = new Link("Voltar",'administracao.php?fase=importacao');
            $link->set_class('button');
            $link->set_title('Volta para a página anterior');
            $link->set_accessKey('V');
            $menu->add_link($link,"left");
            
            # 2017
            $link = new Link("Analisar","?fase=aguarda");
            $link->set_class('button');
            $link->set_title('Importando tabela de afastamento os lançamentos tipo 10 e 33');
            $menu->add_link($link,"right"); 
            
            # Cria um menu
            $menu->show();
            
            titulo('Importação de Dependentes de arquivo texto para o banco de dados');
            break;
        
        #########################################################################
    
        case "aguarda" :
            titulo('Analisando ...');
            br(4);
            aguarde("Analisando o arquivo de faltas");

            loadPage('?fase=analisa');
            break;
        
        #########################################################################

        case "analisa" :            
            # Cria um menu
            $menu = new MenuBar();
            
            # Define o arquivo a ser importado
            $arquivo = "../importacao/dependentes.txt"; 

            # Abre o banco de dados
            $pessoal = new Pessoal();

            # Botão voltar
            $linkBotao1 = new Link("Voltar",'?');
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página anterior');
            $linkBotao1->set_accessKey('V');
            $menu->add_link($linkBotao1,"left");

            # Refazer
            $linkBotao2 = new Link("Refazer",'?fase=aguarda');
            $linkBotao2->set_class('button');
            $linkBotao2->set_title('Refazer a Importação');
            $linkBotao2->set_accessKey('R');
            $menu->add_link($linkBotao2,"right");
            $menu->show();

            titulo('Importação da tabela de Faltas');

            # Cria um painel
            $painel = new Callout();
            $painel->abre();

            # Verifica a existência do arquivo
            if(file_exists($arquivo)){
                $lines = file($arquivo);
                
                # Inicia a tabela
                echo "<table border=1>";

                echo "<tr>";
                echo "<th>#</th>";
                echo "<th>IdFuncional</th>";
                echo "<th>IdServidor</th>";
                echo "<th>Nome</th>";
                echo "<th>Dependente</th>";
                echo "<th>Nascimento</th>";
                echo "<th>CPF</th>";
                echo "<th>Parentesco</th>";
                echo "<th>Sexo</th>";
                echo "</tr>";

                # Percorre o arquivo e guarda os dados em um array
                foreach ($lines as $linha) {
                    
                    # Retira lixos de formatação
                    $linha = htmlspecialchars($linha);

                    # Divide as colunas
                    $parte = explode(";",$linha);
                    
                    # Passa para as variáveis
                    $idFuncional = $parte[0];
                    
                    if(!vazio($idFuncional)){
                        $idServidor = $pessoal->get_idServidoridFuncional($idFuncional);
                        
                        if(!vazio($idServidor)){
                            $nome1 = $pessoal->get_Nome($idServidor);
                            $nome2 = $parte[2];
                            $dependente = $parte[10];
                            $nascimento = $parte[13];
                            $cpf = $parte[12];
                            $parentesco = $parte[11];
                            $sexo = $parte[14];

                            # Analisa o parentesco
                            $idParentesco = NULL;
                            $nomeParentesco = NULL;
                            switch ($parentesco){       
                                case "FILHO(A)" : 
                                    $idParentesco = 2;
                                    break;

                                case "CÔNJUGE" : 
                                    $idParentesco = 1;
                                    break;

                                case "GUARDA PROVISÓRIA" : 
                                    $idParentesco = 9;
                                    break;

                                case "COMPANHEIRO(A)" : 
                                    $idParentesco = 11;
                                    break;

                                case "ENTEADO(A)" : 
                                    $idParentesco = 10;
                                    break;

                                case "COTISTA" :
                                    $parentesco = "Cotista";
                                    break;

                                case "PAI/MÃE" :
                                    if($sexo == "F"){
                                        $idParentesco = 4;
                                    }else{
                                        $idParentesco = 3;
                                    }
                                    break;
                            }

                            if(!is_null($idParentesco)){
                                $nomeParentesco = $pessoal->get_parentesco($idParentesco);
                            }
                            $contador++;

                            # Exibe os dados
                            echo "<tr>";
                            echo "<td>".$contador."</td>";
                            echo "<td>".$idFuncional."</td>";
                            echo "<td>".$idServidor."</td>";
                            echo "<td>".$nome1."<br/>".$nome2."</td>";
                            echo "<td>".$dependente."</td>";
                            echo "<td>".$nascimento."</td>";
                            echo "<td>".$cpf."</td>";
                            echo "<td>".$parentesco."<br/>".$nomeParentesco."</td>";
                            echo "<td>".$sexo."</td>";
                            echo "</tr>";
                        }else{
                            $problemas++;
                        }
                    }
                }
               
                echo "Registros analisados: ".$contador;
                br();
                echo "Problemas encontrados: ".$problemas;
                br();
            }else{
                echo "Arquivo não encontrado";
                br();
                $problemas++;
            }
            
            #if($problemas == 0){
                echo "Podemos fazer a importação";
                br(2);
                # Botão importar
                $linkBotao1 = new Link("Importar",'?fase=importa');
                $linkBotao1->set_class('button');
                $linkBotao1->set_title('Volta para a página anterior');
                $linkBotao1->set_accessKey('I');
                $linkBotao1->show();
                
            #}else{
            #    echo "Temos problemas";
            #}

            $painel->fecha();
            break;
            
        #########################################################################    
            
        case "importa" :
            titulo('Importando ...');
            br(4);
            aguarde("Importando o arquivo de Faltas.");
            br();
            loadPage('?fase=importa2');
            break;
        
        #########################################################################
        
        case "importa2" :
            # Define o arquivo a ser importado
            $arquivo = "../importacao/dependentes.txt"; 
            
            # Verifica a existência do arquivo
            if(file_exists($arquivo)){
                $lines = file($arquivo);

                # Abre o banco de dados
                $pessoal = new Pessoal();

                # Percorre o arquivo e guarda os dados em um array
                foreach ($lines as $linha) {
                    
                    # Retira lixos de formatação
                    $linha = htmlspecialchars($linha);

                    # Divide as colunas
                    $parte = explode(";",$linha);
                    
                    # Passa para as variáveis
                    $idFuncional = $parte[0];
                    
                    if(!vazio($idFuncional)){
                        $idServidor = $pessoal->get_idServidoridFuncional($idFuncional);
                        
                        if(!vazio($idServidor)){
                            $nome1 = $pessoal->get_Nome($idServidor);
                            $nome2 = $parte[2];
                            $dependente = $parte[10];
                            $nascimento = date_to_bd($parte[13]);
                            $cpf = $parte[12];
                            $parentesco = $parte[11];
                            $sexo = $parte[14];

                            # Analisa o parentesco
                            $idParentesco = NULL;
                            $nomeParentesco = NULL;
                            $cotista = NULL;
                            switch ($parentesco){       
                                case "FILHO(A)" : 
                                    $idParentesco = 2;
                                    break;

                                case "CÔNJUGE" : 
                                    $idParentesco = 1;
                                    break;

                                case "GUARDA PROVISÓRIA" : 
                                    $idParentesco = 9;
                                    break;

                                case "COMPANHEIRO(A)" : 
                                    $idParentesco = 11;
                                    break;

                                case "ENTEADO(A)" : 
                                    $idParentesco = 10;
                                    break;

                                case "COTISTA" :
                                    $cotista = "Sim";
                                    break;

                                case "PAI/MÃE" :
                                    if($sexo == "F"){
                                        $idParentesco = 4;
                                    }else{
                                        $idParentesco = 3;
                                    }
                                    break;
                            }

                            if(!is_null($idParentesco)){
                                $nomeParentesco = $pessoal->get_parentesco($idParentesco);
                            }
                            $contador++;

                            # Verifica se foi encontrado o idFuncional
                            if(vazio($idServidor)){
                                $problemas++;
                            }

                            # Pega o idPessoa
                            $idPessoa = $pessoal->get_idPessoa($idServidor);

                            # Grava na tabela
                            $campos = array("idPessoa","nome","dtNasc","cpf","parentesco","sexo","cotista");
                            $valor = array($idPessoa,$dependente,$nascimento,$cpf,$idParentesco,$sexo,$cotista);                    
                            $pessoal->gravar($campos,$valor,NULL,"tbdependente","idDependente",FALSE);
                        }
                    }
                }
            }else{
                echo "Arquivo não encontrado";
            }
            loadPage("?fase=termina");
            break;
            
        #########################################################################    
            
        case "termina" :
            titulo('Importação Terminada');
            br(4);
            P("Importação executada com sucesso !!");
            br(2);
            
            # Botão importar
            $linkBotao1 = new Link("Ok",'?');
            $linkBotao1->set_class('button');
            $linkBotao1->set_title('Volta para a página Inicial');
            $linkBotao1->set_accessKey('O');
            $linkBotao1->show();
            break;
        
        #########################################################################
    }
    
    $grid->fechaColuna();
    $grid->fechaGrid();        
    $page->terminaPagina();
}