<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\GsBase;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GsBaseXml
 *
 * Contiene las consultas XML a la API XML de GsBase.
 *
 * @package App\GsBase
 */
class GsBaseXml
{
    protected $container;

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    /**
     * @var string
     */
    private $xmlUpdateOperator = '<?xml version="1.0" ?>
        <Consulta>
            <Accion>SELECT</Accion>
            <Tabla>operadores</Tabla>
            <Campos>
                <Campo>OP_CCL</Campo>
                <Campo>OP_REG</Campo>
                <Campo>OP_SREG</Campo>
                <Campo>OP_NOP</Campo>
                <Campo>OP_EST</Campo>
                <Campo>OP_TPEX</Campo>
                <Campo>OP_NAM</Campo>
                <Campo>OP_PVCL</Campo>
                <Campo>OP_PBCL</Campo>
                <Campo>OP_PTAA</Campo>
                <Campo>OP_PTCC</Campo>
                <Campo>OP_TIPO</Campo>
                <Campo>OP_ACT</Campo>
                <Campo>OP_GGN</Campo>
                <Campo>OP_NRGAP</Campo>
                <Campo>OP_FAUD</Campo>
                <Campo>OP_CLP</Campo>
            </Campos>
            <Preguntas>
                <Pregunta>
                    <Campo>OP_NAM</Campo>
                    <Ope>=</Ope>
                    <Valor>0</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_EST</Campo>
                    <Ope>dif</Ope>
                    <Valor>B</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_EST</Campo>
                    <Ope>dif</Ope>
                    <Valor>N</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_EST</Campo>
                    <Ope>dif</Ope>
                    <Valor>D</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_TPEX</Campo>
                    <Ope>dif</Ope>
                    <Valor>P</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_NAM</Campo>
                    <Ope>dif</Ope>
                    <Valor>10</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_NAM</Campo>
                    <Ope>dif</Ope>
                    <Valor>20</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_NAM</Campo>
                    <Ope>dif</Ope>
                    <Valor>30</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_NAM</Campo>
                    <Ope>dif</Ope>
                    <Valor>40</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_NAM</Campo>
                    <Ope>dif</Ope>
                    <Valor>50</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FBS</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>VCA</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>IDL</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>IBR</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>DL</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FBR</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>PAE</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FCG</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FGE</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FHC</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FKO</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FBR</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FOP</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FOZ</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FVA</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FVC</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FVC09</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FVF</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FVG</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FVI</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FVL</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FVN</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FVR</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FVS</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FVX</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FVZ</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>EOZ</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FVR</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FVR</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_SREG</Campo>
                    <Ope>dif</Ope>
                    <Valor>FVR</Valor>
                </Pregunta>
            </Preguntas>
            <Relacionados>
                <Relacion id="OP_REGPAD">
                    <Origen>OP_REG</Origen>
                    <Tabla>registros</Tabla>
                    <Campo>RE_PAD</Campo>
                </Relacion>
                <Relacion id="OP_ALCAN">
                    <Origen>OP_REG</Origen>
                    <Tabla>registros</Tabla>
                    <Campo>RE_DENO</Campo>
                </Relacion>
                <Relacion id="OP_DENOOP">
                    <Origen>OP_CCL</Origen>
                    <Tabla>clientes</Tabla>
                    <Campo>CL_DENO</Campo>
                </Relacion>
                <Relacion id="OP_CIF">
                    <Origen>OP_CCL</Origen>
                    <Tabla>clientes</Tabla>
                    <Campo>CL_CIF</Campo>
                </Relacion>
                <Relacion id="OP_SREGDENO">
                    <Origen>OP_SREG</Origen>
                    <Tabla>registros</Tabla>
                    <Campo>RE_DENO</Campo>
                </Relacion>
                <Relacion id="OP_CDP">
                    <Origen>OP_CCL</Origen>
                    <Tabla>clientes</Tabla>
                    <Campo>CL_CDP</Campo>
                </Relacion>
                <Relacion id="OP_DOMOP">
                    <Origen>OP_CCL</Origen>
                    <Tabla>clientes</Tabla>
                    <Campo>CL_DOM</Campo>
                </Relacion>
                <Relacion id="OP_TEL">
                    <Origen>OP_CCL</Origen>
                    <Tabla>clientes</Tabla>
                    <Campo>CL_TEL</Campo>
                </Relacion>
                <Relacion id="OP_TECDENO">
                    <Origen>OP_TEC</Origen>
                    <Tabla>tecnicos</Tabla>
                    <Campo>TN_DENO</Campo>
                </Relacion>
                <Relacion id="OP_EMA">
                    <Origen>OP_CCL</Origen>
                    <Tabla>clientes</Tabla>
                    <Campo>CL_EMA</Campo>
                </Relacion>
                <Relacion id="OP_TECEMA">
                    <Origen>OP_TEC</Origen>
                    <Tabla>tecnicos</Tabla>
                    <Campo>TN_EMA</Campo>
                </Relacion>
            </Relacionados>
            </Consulta>';

    private $xmlUpdateRegistry = '<?xml version="1.0" ?>
        <Consulta>
            <Accion>SELECT</Accion>
            <Tabla>registros</Tabla>
            <Campos>
                <Campo>RE_DENO</Campo>
                <Campo>RE_TIPO</Campo>
                <Campo>RE_PAD</Campo>
                <Campo>RE_ACT</Campo>
            </Campos>
            <Preguntas/>
            <Relacionados/>
        </Consulta>';

    /**
     * @var string
     */
    private $xmlUpdateTiposCultivos = '<?xml version="1.0" ?>
        <Consulta>
            <Accion>SELECT</Accion>
            <Tabla>tipos-cultivo</Tabla>
            <Campos>
                <Campo>TC_DENO</Campo>
                <Campo>TC_ROAE</Campo>
            </Campos>
            <Preguntas/>
            <Relacionados/>
        </Consulta>';

    /**
     * @var string
     */
    private $xmlUpdateTiposProducto = '<?xml version="1.0" ?>
        <Consulta>
            <Accion>SELECT</Accion>
            <Tabla>tipos-producto</Tabla>
            <Campos>
                <Campo>TI_DENO</Campo>
            </Campos>
            <Preguntas/>
            <Relacionados/>
        </Consulta>';

    /**
     * @var string
     */
    private $xmlUpdateCultivos = '<?xml version="1.0" ?>
        <Consulta>
            <Accion>SELECT</Accion>
            <Tabla>cultivos</Tabla>
            <Campos>
                <Campo>CU_DENO</Campo>
                <Campo>CU_ROAE</Campo>
            </Campos>
            <Preguntas/>
            <Relacionados/>
        </Consulta>';

    /**
     * @var string
     */
    private $xmlUpdateEspecies = '<?xml version="1.0" ?>
        <Consulta>
            <Accion>SELECT</Accion>
            <Tabla>especies</Tabla>
            <Campos>
                <Campo>ES_DENO</Campo>
            </Campos>
            <Preguntas/>
            <Relacionados/>
        </Consulta>';

    /**
     * @var string
     */
    private $xmlUpdateProductosG = '<?xml version="1.0" ?>
        <Consulta>
            <Accion>SELECT</Accion>
            <Tabla>productos-g</Tabla>
            <Campos>
                <Campo>PN_DENO</Campo>
            </Campos>
            <Preguntas/>
            <Relacionados/>
        </Consulta>';

    /**
     * @var string
     */
    private $xmlUpdateProductos = '<?xml version="1.0" ?>
        <Consulta>
            <Accion>SELECT</Accion>
            <Tabla>productos</Tabla>
            <Campos>
                <Campo>PT_DENO</Campo>
                <Campo>PT_TC</Campo>
                <Campo>PT_CU</Campo>
                <Campo>PT_TI</Campo>
            </Campos>
            <Preguntas/>
            <Relacionados/>
        </Consulta>';

    /**
     * @var string
     */
    private $xmlUpdateActividadesI = '<?xml version="1.0" ?>
        <Consulta>
            <Accion>SELECT</Accion>
            <Tabla>actividades-i</Tabla>
            <Campos>
                <Campo>AIN_DENO</Campo>
            </Campos>
            <Preguntas/>
            <Relacionados/>
        </Consulta>';

    /**
     * @var string
     */
    private $xmlUpdateCultivosRec = '';

    /**
     * @var string
     */
    private $xmlUpdateProductosIndus = '';

    /**
     * @var string
     */
    private $xmlUpdateAvesCorral = '';

    /**
     * @var string
     */
    private $xmlUpdateTiposProducc = '<?xml version="1.0" ?>
        <Consulta>
            <Accion>SELECT</Accion>
            <Tabla>tipos-producc</Tabla>
            <Campos>
                <Campo>TPN_DENO</Campo>
            </Campos>
            <Preguntas/>
            <Relacionados/>
        </Consulta>';

    /***
     * @var string
     */
    private $xmlUpdateProductosPae = '';

    /**
     * @var string
     */
    private $xmlUpdateGanaderias = '';

    /**
     * @var string
     */
    private $xmlUpdateCultivosRec2 = '';

    /**
     * @var string
     */
    private $xmlUpdateIAvesCorral = '';

    /**
     * @var string
     */
    private $xmlUpdateIndustriasNop = '';

    /**
     * @var string
     */
    private $xmlUpdateIndustriasCcl = '';

    /**
     * @var string
     */
    private $xmlRemoveOperator = '<?xml version="1.0" ?>
        <Consulta>
            <Accion>SELECT</Accion>
            <Tabla>operadores</Tabla>
            <Campos>
                <Campo>OP_NOP</Campo>
                <Campo>OP_EST</Campo>
            </Campos>
            <Preguntas>
                <Pregunta>
                    <Campo>OP_EST</Campo>
                    <Ope>dif</Ope>
                    <Valor>V</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_EST</Campo>
                    <Ope>dif</Ope>
                    <Valor>A</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_EST</Campo>
                    <Ope>dif</Ope>
                    <Valor>E</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_EST</Campo>
                    <Ope>dif</Ope>
                    <Valor>F</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_EST</Campo>
                    <Ope>dif</Ope>
                    <Valor>C</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_EST</Campo>
                    <Ope>dif</Ope>
                    <Valor>P</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_EST</Campo>
                    <Ope>dif</Ope>
                    <Valor>R</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_EST</Campo>
                    <Ope>dif</Ope>
                    <Valor>QT</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_EST</Campo>
                    <Ope>dif</Ope>
                    <Valor>X</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_EST</Campo>
                    <Ope>dif</Ope>
                    <Valor>I</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_EST</Campo>
                    <Ope>dif</Ope>
                    <Valor>T</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_EST</Campo>
                    <Ope>dif</Ope>
                    <Valor>O</Valor>
                </Pregunta>
                <Pregunta>
                    <Campo>OP_EST</Campo>
                    <Ope>dif</Ope>
                    <Valor>S</Valor>
                </Pregunta>
            </Preguntas>
            <Relacionados/>
        </Consulta>';

    /**
     * @var string
     */
    private $xmlUpdateDocPresu = '';

    /**
     * @var string
     */
    private $xmlUpdateClient = '';

    /**
     * @var string
     */
    private $xmlRetrieveExpDocList = '';

    /**
     * @var string
     */
    private $xmlRetrieveExpDocFile = '';

    /**
     * @var string
     */
    private $xmlRetrieveAnaList = '';

    /**
     * @var string
     */
    private $xmlRetrieveDocAna = '';

    /**
     * @var string
     */
    private $xmlRetrieveContact = '';

    /**
     * UpdateOperator
     *
     * Contiene la consulta XML a gsBase para actualizar los Operadores.
     *
     * Excluye de la misma a los Operadores de subRegistro:
     * 'FBR',
     * 'FCG',
     * 'FGE',
     * 'FHC',
     * 'FKO',
     * 'FOP',
     * 'FPI',
     * 'FOZ',
     * 'FVA',
     * 'FVC',
     * 'FVC09',
     * 'FVF',
     * 'FVG',
     * 'FVI',
     * 'FVL',
     * 'FVN',
     * 'FVR',
     * 'FVS',
     * 'FVX',
     * 'FVZ',
     * 'EOZ',
     * 'FBS',
     * 'VCA',
     * 'IDL',
     * 'IBR',
     * 'DL'
     *
     * opEst:
     * 'B',
     * 'N',
     * 'D'
     *
     * opTpex:
     *  'P'
     *
     * @return string
     */
    public function getXmlUpdateOperator()
    {
        return $this->xmlUpdateOperator;
    }

    /**
     * UpdateRegistry
     *
     * Contiene la consulta XML a gsBase para actualizar la tabla de Registros.
     *
     * @return string
     */
    public function getXmlUpdateRegistry()
    {
        return $this->xmlUpdateRegistry;
    }

    /**
     * UpdateTiposCultivos
     *
     * Contiene la consulta XML a gsBase para actualizar la tabla de TiposCultivos.
     *
     * @return string
     */
    public function getXmlUpdateTiposCultivos()
    {
        return $this->xmlUpdateTiposCultivos;
    }

    /**
     * UpdateTiposProducto
     *
     * Contiene la consulta XML a gsBase para actualizar la tabla de TiposProducto.
     *
     * @return string
     */
    public function getXmlUpdateTiposProducto()
    {
        return $this->xmlUpdateTiposProducto;
    }

    /**
     * UpdateCultivos
     *
     * Contiene la consulta XML a gsBase para actualizar la tabla de Cultivos.
     *
     * @return string
     */
    public function getXmlUpdateCultivos()
    {
        return $this->xmlUpdateCultivos;
    }

    /**
     * UpdateEspecies
     *
     * Contiene la consulta XML a gsBase para actualizar la tabla de Especies.
     *
     * @return string
     */
    public function getXmlUpdateEspecies()
    {
        return $this->xmlUpdateEspecies;
    }

    /**
     * UpdateProductosG
     *
     * Contiene la consulta XML a gsBase para actualizar la tabla de ProductosG.
     *
     * @return string
     */
    public function getXmlUpdateProductosG()
    {
        return $this->xmlUpdateProductosG;
    }

    /**
     * UpdateProductos
     *
     * Contiene la consulta XML a gsBase para actualizar la tabla de Productos.
     *
     * @return string
     */
    public function getXmlUpdateProductos()
    {
        return $this->xmlUpdateProductos;
    }

    /**
     * UpdateActividadesI
     *
     * Contiene la consulta XML a gsBase para actualizar la tabla de ActividadesI.
     *
     * @return string
     */
    public function getXmlUpdateActividadesI()
    {
        return $this->xmlUpdateActividadesI;
    }

    /**
     * UpdateCultivosRec
     *
     * Contiene la consulta XML a gsBase para actualizar un registro de CultivosRec.
     *
     * @param string $nop El número del Operador vinculado a un registro de CultivosRec
     * @return string
     */
    public function getXmlUpdateCultivosRec($nop)
    {
        $this->xmlUpdateCultivosRec = '<?xml version="1.0" ?>
            <Consulta>
                <Accion>SELECT</Accion>
                <Tabla>cultivos_rec</Tabla>
                <Campos>
                    <Campo>RU_TPPR</Campo>
                    <Campo>RU_PRO</Campo>
                    <Campo>RU_SITR</Campo>
                    <Campo>RU_NOP</Campo>
                    <Campo>RU_CUL</Campo>
                    <Campo>RU_ACT</Campo>
                    <Campo>RU_RC</Campo>
                </Campos>
                <Preguntas>
                    <Pregunta>
                        <Campo>RU_NOP</Campo>
                        <Ope>=</Ope>
                        <Valor>' . $nop . '</Valor>
                    </Pregunta>
                    <Pregunta>
                        <Campo>RU_SITR</Campo>
                        <Ope>=</Ope>
                        <Valor>L</Valor>
                    </Pregunta>
                </Preguntas>
                <Relacionados/>
            </Consulta>';

        return $this->xmlUpdateCultivosRec;
    }

    /**
     * UpdateProductosIndus
     *
     * Contiene la consulta XML a gsBase para actualizar un registro de ProductosIndus.
     *
     * @param string $nop El NOP de un Operador vinculado a un ProductoIndus
     * @return string
     */
    public function getXmlUpdateProductosIndus($nop)
    {
        return $this->xmlUpdateProductosIndus = '<?xml version="1.0" ?>
            <Consulta>
                <Accion>SELECT</Accion>
                <Tabla>productos-indus</Tabla>
                <Campos>
                    <Campo>PI_PRO</Campo>
                    <Campo>PI_MARCA</Campo>
                    <Campo>PI_NOP</Campo>
                    <Campo>PI_DPRO</Campo>
                    <Campo>PI_CPRO</Campo>
                </Campos>
                <Preguntas>
                    <Pregunta>
                        <Campo>PI_NOP</Campo>
                        <Ope>=</Ope>
                        <Valor>' . $nop . '</Valor>
                    </Pregunta>
                </Preguntas>
                <Relacionados/>
            </Consulta>';
    }

    /**
     * UpdateAvesCorral
     *
     * Contiene la consulta XML a gsBase para actualizar un registro de AvesCorral.
     *
     * @param string $nop El NOP de un Operador vinculado a un registro AvesCorral
     * @return string
     */
    public function getXmlUpdateAvesCorral($nop)
    {
        return $this->xmlUpdateAvesCorral = '<?xml version="1.0" ?>
            <Consulta>
                <Accion>SELECT</Accion>
                <Tabla>avescorral</Tabla>
                <Campos>
                    <Campo>AVC_ESP</Campo>
                    <Campo>AVC_TPN</Campo>
                    <Campo>AVC_NOP</Campo>
                </Campos>
                <Preguntas>
                    <Pregunta>
                        <Campo>AVC_NOP</Campo>
                        <Ope>=</Ope>
                        <Valor>' . $nop . '</Valor>
                    </Pregunta>
                <Preguntas/>
                <Relacionados/>
            </Consulta>';
    }

    /**
     * UpdateTiposProducc
     *
     * Contiene la consulta XML a gsBase para actualizar un registro de TiposProducc.
     *
     * @return string
     */
    public function getXmlUpdateTiposProducc()
    {
        return $this->xmlUpdateTiposProducc;
    }

    /**
     * UpdateProductosPae
     *
     * Contiene la consulta XML a gsBase para actualizar un registro de ProductosPae.
     *
     * Como condición el PIP_EST debe ser L
     *
     * @param string $nop El NOP del Operador asociado al registro de ProductosPae
     * @return string
     */
    public function getXmlUpdateProductosPae($nop)
    {
        return $this->xmlUpdateProductosPae = '<?xml version="1.0" ?>
            <Consulta>
                <Accion>SELECT</Accion>
                <Tabla>productos-pae</Tabla>
                <Campos>
                    <Campo>PIP_PRO</Campo>
                    <Campo>PIP_DSC</Campo>
                    <Campo>PIP_NOP</Campo>
                    <Campo>PIP_TPP</Campo>
                    <Campo>PIP_EST</Campo>
                </Campos>
                <Preguntas>
                    <Pregunta>
                        <Campo>PIP_NOP</Campo>
                        <Ope>=</Ope>
                        <Valor>' . $nop . '</Valor>
                    </Pregunta>
                    <Pregunta>
                        <Campo>PIP_EST</Campo>
                        <Ope>=</Ope>
                        <Valor>L</Valor>
                    </Pregunta>
                </Preguntas>
                <Relacionados/>
            </Consulta>';

    }

    /**
     * UpdateGanaderias
     *
     * Contiene la consulta XML a gsBase para actualizar un registro de Ganaderias vinculado a un Operador.
     *
     * @param string $nop El NOP del Operador vinculado al registro de Ganaderias.
     * @return string
     */
    public function getXmlUpdateGanaderias($nop)
    {
        return $this->xmlUpdateGanaderias = '<?xml version="1.0" ?>
            <Consulta>
                <Accion>SELECT</Accion>
                <Tabla>ganaderias</Tabla>
                <Campos>
                    <Campo>GN_RC</Campo>
                    <Campo>GN_ESP</Campo>
                    <Campo>GN_TPN</Campo>
                    <Campo>GN_PRO</Campo>
                    <Campo>GN_TPCU</Campo>
                    <Campo>GN_NOP</Campo>
                    <Campo>GN_RAZA</Campo>
                    <Campo>GN_UCAP</Campo>
                </Campos>
                <Preguntas>
                    <Pregunta>
                        <Campo>GN_NOP</Campo>
                        <Ope>=</Ope>
                        <Valor>' . $nop . '</Valor>
                    </Pregunta>
                </Preguntas>
                <Relacionados/>
            </Consulta>';
    }

    /**
     * UpdateCultivosRec2
     *
     * Contiene la consulta XML a gsBase para actualizar un registro de CultivosRec2 vinculado a un Operador.
     *
     * @param string $nop El NOP del Operador vinculado al registro CultivosRec2
     * @return string
     */
    public function getXmlUpdateCultivosRec2($nop)
    {
        return $this->xmlUpdateCultivosRec2 = '<?xml version="1.0" ?>
            <Consulta>
                <Accion>SELECT</Accion>
                <Tabla>cultivos_rec2</Tabla>
                <Campos>
                    <Campo>RU2_FEC</Campo>
                    <Campo>RU2_NOP</Campo>
                    <Campo>RU2_POL</Campo>
                    <Campo>RU2_PAR</Campo>
                    <Campo>RU2_CUL</Campo>
                    <Campo>RU2_REC</Campo>
                    <Campo>RU2_SIT</Campo>
                </Campos>
                <Preguntas>
                    <Pregunta>
                        <Campo>RU2_NOP</Campo>
                        <Ope>=</Ope>
                        <Valor>' . $nop . '</Valor>
                    </Pregunta>
                    <Pregunta>
                        <Campo>RU2_SIT</Campo>
                        <Ope>=</Ope>
                        <Valor>L</Valor>
                    </Pregunta>
                </Preguntas>
                <Relacionados/>
                </Consulta>';
    }

    /**
     * UpdateIAvesCorral
     *
     * Contiene la consulta XML a gsBase para actualizar un registro de IAvesCorral vinculado a un Operador.
     * @param $nop
     * @return string
     */
    public function getXmlUpdateIAvesCorral($nop)
    {
        return $this->xmlUpdateIAvesCorral = '<?xml version="1.0" ?>
            <Consulta>
                <Accion>SELECT</Accion>
                <Tabla>iavescorral</Tabla>
                <Campos>
                    <Campo>AVI_PRD</Campo>
                    <Campo>AVI_VAR</Campo>
                    <Campo>AVI_IND</Campo>
                    <Campo>AVI_MAR</Campo>
                    <Campo>AVI_NOP</Campo>
                </Campos>
                <Preguntas>
                    <Pregunta>
                        <Campo>AVI_NOP</Campo>
                        <Ope>=</Ope>
                        <Valor>' . $nop . '</Valor>
                    </Pregunta>
                </Preguntas>
                <Relacionados/>
            </Consulta>';
    }

    /**
     * UpdateIndustrias por NOP
     *
     * Contiene la consulta XML a gsBase para actualizar un registro de Industrias vinculado a un Operador.
     *
     * @param string $nop El NOP del Operador vinculado al registro de Industrias
     * @return string
     */
    public function getXmlUpdateIndustriasNop($nop)
    {
        return $this->xmlUpdateIndustriasNop = '<?xml version="1.0" ?>
            <Consulta>
                <Accion>SELECT</Accion>
                <Tabla>industrias</Tabla>
                <Campos>
                    <Campo>IN_ACT</Campo>
                    <Campo>IN_SIT</Campo>
                    <Campo>IN_NOP</Campo>
                    <Campo>IN_CCL</Campo>
                    <Campo>IN_DOM</Campo>
                    <Campo>IN_TEL</Campo>
                    <Campo>IN_CDP</Campo>
                    <Campo>IN_POB</Campo>
                    <Campo>IN_PROV</Campo>
                </Campos>
                <Preguntas>
                    <Pregunta>
                        <Campo>IN_NOP</Campo>
                        <Ope>=</Ope>
                        <Valor>' . $nop . '</Valor>
                    </Pregunta>
                </Preguntas>
                <Relacionados/>
            </Consulta>';
    }

    /**
     * UpdateIndustrias por CCL
     *
     * Contiene la consulta XML a gsBase para actualizar un registro de Industrias vinculado a un Operador.
     *
     * @param string $ccl El opCcl del Operador vinculado al registro de Industrias
     * @return string
     */
    public function getXmlUpdateIndustriasCcl($ccl)
    {
        return $this->xmlUpdateIndustriasCcl = '<?xml version="1.0" ?>
            <Consulta>
                <Accion>SELECT</Accion>
                <Tabla>industrias</Tabla>
                <Campos>
                    <Campo>IN_ACT</Campo>
                    <Campo>IN_SIT</Campo>
                    <Campo>IN_NOP</Campo>
                    <Campo>IN_CCL</Campo>
                    <Campo>IN_DOM</Campo>
                    <Campo>IN_TEL</Campo>
                    <Campo>IN_CDP</Campo>
                    <Campo>IN_POB</Campo>
                    <Campo>IN_PROV</Campo>
                </Campos>
                <Preguntas>
                    <Pregunta>
                        <Campo>IN_CCL</Campo>
                        <Ope>=</Ope>
                        <Valor>' . $ccl . '</Valor>
                    </Pregunta>
                </Preguntas>
                <Relacionados/>
            </Consulta>';
    }

    /**
     * RemoveOperator
     *
     * Contiene la consulta XML a gsBase para los Operadores a eliminar, que son los considerados con opEst distinto de:
     *  V
     *  A
     *  E
     *  F
     *  C
     *  P
     *  R
     *  QT
     *  X
     *  I
     *  T
     *
     * @return string
     */
    public function getXmlRemoveOperator()
    {
        return $this->xmlRemoveOperator;
    }

    /**
     * UpdateDocPresu
     *
     * Contiene la consulta XML a gsBase para los DocPresu de un cliente.
     *
     * @param string $ccl El CCL de un cliente vinculado a un DocPresu
     * @return string
     */
    public function getXmlUpdateDocPresuCcl($ccl)
    {
        return $this->xmlUpdateDocPresu = '<?xml version="1.0" ?>
            <Consulta>
                <Accion>SELECT</Accion>
                <Tabla>doc-presu</Tabla>
                <Campos>
                    <Campo>DDC_DENO</Campo>
                    <Campo>DDC_CCL</Campo>
                    <Campo>DDC_EST</Campo>
                    <Campo>DDC_EST</Campo>
                    <Campo>DDC_FEC</Campo>
                    <Campo>DDC_PRE0</Campo>
                </Campos>
                <Preguntas>
                    <Pregunta>
                        <Campo>DDC_CCL</Campo>
                        <Ope>=</Ope>
                        <Valor>' . $ccl . '</Valor>
                    </Pregunta>
                </Preguntas>
                <Relacionados/>
            </Consulta>';
    }

    /**
     * UpdateDocPresu
     *
     * Contiene la consulta XML a gsBase para un DocPresu determinado.
     *
     * @param string $codigo El codigo en gsBase de un DocPresu
     * @return string
     */
    public function getXmlDocPresuCodigo($codigo)
    {
        return $this->xmlUpdateDocPresu = '<?xml version="1.0" ?>
            <Consulta>
                <Accion>SELECT</Accion>
                <Tabla>doc-presu</Tabla>
                <Campos>
                    <Campo>DDC_DENO</Campo>
                    <Campo>DDC_CCL</Campo>
                    <Campo>DDC_EST</Campo>
                    <Campo>DDC_EST</Campo>
                    <Campo>DDC_TXT</Campo>
                    <Campo>DDC_PRE0</Campo>
                </Campos>
                <Preguntas>
                    <Pregunta>
                        <Campo>0</Campo>
                        <Ope>=</Ope>
                        <Valor>' . $codigo . '</Valor>
                    </Pregunta>
                </Preguntas>
                <Relacionados>
                    <Relacion id="DDC_CIF">
                        <Origen>DDC_CCL</Origen>
                        <Tabla>clientes</Tabla>
                        <Campo>CL_CIF</Campo>
                    </Relacion>
                </Relacionados>
            </Consulta>';
    }

    /**
     * UpdateClient
     *
     * Contiene la consulta XML a gsBase para obtener los Clientes asoaciados a un CIF.
     *
     * Como condicion se especifica que CL_BLQ sea distinto de S.
     *
     * @param string $opCcl el codigo gsBase de un Cliente
     * @return string
     */
    public function getXmlUpdateClient($opCcl)
    {
        return $this->xmlUpdateClient = '<?xml version="1.0" ?>
            <Consulta>
                <Accion>SELECT</Accion>
                <Tabla>clientes</Tabla>
                <Campos>
                    <Campo>CL_DENO</Campo>
                    <Campo>CL_DOM</Campo>
                    <Campo>CL_POB</Campo>
                    <Campo>CL_PROV</Campo>
                    <Campo>CL_CDP</Campo>
                    <Campo>CL_TEL</Campo>
                    <Campo>CL_FAX</Campo>
                    <Campo>CL_CIF</Campo>
                    <Campo>CL_EMA</Campo>
                    <Campo>CL_PAIS</Campo>
                    <Campo>CL_ACTI</Campo>
                </Campos>
                <Preguntas>
                    <Pregunta>
                        <Campo>0</Campo>
                        <Ope>=</Ope>
                        <Valor>' . $opCcl . '</Valor>
                    </Pregunta>
                    <Pregunta>
                        <Campo>CL_BLQ</Campo>
                        <Ope>dif</Ope>
                        <Valor>S</Valor>
                    </Pregunta>
                </Preguntas>
                <Relacionados/>
            </Consulta>';
    }

    /**
     * RetrieveExpDocList
     *
     * Contiene la consulta XML a gsBase para obtener el listado de DocExp vinculados a un NOP de Operador.
     *
     * @param string $nop El NOP de un Operador vinculado a los DocExp
     * @return string
     */
    public function getXmlRetrieveExpDocList($nop)
    {
        return $this->xmlRetrieveExpDocList = '<?xml version="1.0" ?>
            <Consulta>
                <Accion>SELECT</Accion>
                <Tabla>doc-expedientes</Tabla>
                <Campos>
                    <Campo>DE_DENO</Campo>
                    <Campo>DE_FEC</Campo>
                    <Campo>DE_PLA</Campo>
                    <Campo>DE_OPE</Campo>
                    <Campo>DE_CCL</Campo>
                </Campos>
                <Preguntas>
                    <Pregunta>
                        <Campo>DE_OPE</Campo>
                        <Ope>=</Ope>
                        <Valor>' . $nop . '</Valor>
                    </Pregunta>
                </Preguntas>
                <Relacionados/>
            </Consulta >';
    }

    /**
     * RetrieveExpDocFile
     *
     * Contiene la consulta XML a gsBase para obtener un DocExp determinado.
     *
     * @param string $id El codigo en gsBase del DocExp determinado
     * @return string
     */
    public function getXmlRetrieveExpDocFile($id)
    {
        return $this->xmlRetrieveExpDocFile = '<?xml version="1.0" ?>
            <Consulta>
                <Accion>SELECT</Accion>
                <Tabla>doc-expedientes</Tabla>
                <Campos>
                    <Campo>DE_DENO</Campo>
                    <Campo>DE_FEC</Campo>
                    <Campo>DE_PLA</Campo>
                    <Campo>DE_OPE</Campo>
                    <Campo>DE_CCL</Campo>
                    <Campo>DE_TXT</Campo>
                </Campos>
                <Preguntas>
                    <Pregunta>
                        <Campo>0</Campo>
                        <Ope>=</Ope>
                        <Valor>' . $id . '</Valor>
                    </Pregunta>
                </Preguntas>
                <Relacionados/>
            </Consulta >';
    }

    /**
     * RetrieveAnaList
     *
     * Contiene la consulta XML a gsBase para obtener el listado de análisis disponibles para un Oprador.
     *
     * @param string $opCod El codigo gsBase del registro de un Operador
     * @return string
     */
    public function getXmlRetrieveAnaList($opCod)
    {
        return $this->xmlRetrieveAnaList = '<?xml version="1.0" ?>
            <Consulta>
                <Accion>SELECT</Accion>
                <Tabla>analisis</Tabla>
                <Campos>
                    <Campo>AN_FEC</Campo>
                    <Campo>AN_OPE</Campo>
                    <Campo>AN_EST</Campo>
                </Campos>
                <Preguntas>
                    <Pregunta>
                        <Campo>AN_OPE</Campo>
                        <Ope>=</Ope>
                        <Valor>' . $opCod . '</Valor>
                    </Pregunta>
                </Preguntas>
                <Relacionados/>
            </Consulta >';
    }

    /**
     * RetrieveDocAna
     *
     * Contiene la consulta XML a gsBase para obtener un docAna a partir del código de un registro de analisis.
     *
     * @param string $cod El código gsBase de un registro de analisis
     * @return string
     */
    public function getXmlRetrieveDocAna($cod)
    {
        return $this->xmlRetrieveDocAna = '<?xml version="1.0" ?>
            <Consulta>
                <Accion>SELECT</Accion>
                <Tabla>danalisis</Tabla>
                <Campos>
                    <Campo>DAN_FEC</Campo>
                    <Campo>DAN_ANA</Campo>
                    <Campo>DAN_TXT</Campo>
                </Campos>
                <Preguntas>
                    <Pregunta>
                        <Campo>DAN_ANA</Campo>
                        <Ope>=</Ope>
                        <Valor>' . $cod . '</Valor>
                    </Pregunta>
                </Preguntas>
                <Relacionados/>
            </Consulta>';
    }

    /**
     * RetrieveContact
     *
     * Contiene la consulta XML a gsBase para obtener los contactos de un Cliente.
     *
     * @param string $cod El código gsBase de un registro Cliente
     * @return string
     */
    public function getXmlRetrieveContact($cod)
    {
        return $this->xmlRetrieveContact = '<?xml version="1.0" ?>
            <Consulta>
                <Accion>SELECT</Accion>
                <Tabla>contactos</Tabla>
                <Campos>
                    <Campo>CN_CDCL</Campo>
                    <Campo>CN_DENO</Campo>
                    <Campo>CN_APE1</Campo>
                    <Campo>CN_APE2</Campo>
                </Campos>
                <Preguntas>
                    <Pregunta>
                        <Campo>CN_CDCL</Campo>
                        <Ope>=</Ope>
                        <Valor>' . $cod . '</Valor>
                    </Pregunta>
                </Preguntas>
                <Relacionados/>
            </Consulta>';
    }

    /**
     * RetrieveAllContacts
     *
     * Contiene la consulta XML a gsBase para obtener los contactos de un Cliente.
     *
     * @return string
     */
    public function getXmlRetrieveAllContacts()
    {
        return $this->xmlRetrieveContact = '<?xml version="1.0" ?>
            <Consulta>
                <Accion>SELECT</Accion>
                <Tabla>contactos</Tabla>
                <Campos>
                    <Campo>CN_CDCL</Campo>
                    <Campo>CN_DENO</Campo>
                    <Campo>CN_APE1</Campo>
                    <Campo>CN_APE2</Campo>
                </Campos>
                <Preguntas/>
                <Relacionados/>
            </Consulta>';
    }
}
