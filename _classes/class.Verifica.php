<?php
/**
 * classe VerificaAcesso
 * 
 * Classe que verifica o acesso as rotinas
 * 
 * By Alat
 */
class Verifica
{
    /**
     * Método Construtor
     * 
     * @param $matricula string matrícula do servidor logado
     * @param $rotina integer codigo numérico da rotina a ser verificada
     */
    static function acesso($idusuario,$rotina = null)
    {        
        # Flag de permissão do acesso
        $acesso = true;
        $manutencao = false;
        
        $intra = new Intra();
        
        ###########################################################

        /**
         * Verifica se usuário Logou
         */
        
        # Verifica se foi logado se não redireciona para o login
        if(is_null($idusuario))
            $acesso = false;
        
        # Verifica se matricula é nula acesso bloqueado para a área do servidor
        if(($intra->get_senha($idusuario) == '') and ($idusuario <>0))
            $acesso = false;

        # Verifica se o login foi feito ou se a sessão foi "recuperada" pelo browser
        #if (($intra->get_ultimoAcesso($idusuario)) <> date("Y-m-d"))
        #    $acesso = false;

        # Verifica de o usuário logado tem permissão para essa rotina 
        if(!is_null($rotina)){
            if(!($intra->verificaPermissao($idusuario,$rotina)))
                $acesso = false;
        }
        
        /**
         * Verifica se está em Manutenção
         */
        
        # Verifica o ip da máquina
        $ipManutencao = $intra->get_variavel('manutencao_ip');	// ip isento da mensagem
        $ipMaquina = $_SERVER['REMOTE_ADDR'];       		// ip da máquina  
        
        # Verifica se está em Manutenção
        if ($intra->get_variavel('manutencao_intranet')){
            if($ipManutencao <> $ipMaquina)
                $manutecao = true;
        }
        
        # Exibe a mensagem de manutenção
        if($manutencao)
            loadPage("../manutencao.php");
        elseif($acesso)
            return $acesso;
        else{
            #loadPage("../../admin/adminSistema/login.php");
        }
    }    
}
