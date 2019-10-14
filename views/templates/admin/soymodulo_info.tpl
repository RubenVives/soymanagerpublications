{**
* Copyright (c) 2019, Nicripsia Internet SL www.soy.es All rights reserved.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
* INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
* DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
* SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
* SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
* WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE
* USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*
* @author    Soy.es <info@soy.es>
* @copyright 2019 Soy.es
* @license   Soy.es

*}

<div class="soymodulo_info_modulo alert alert-info">
    <div>
        <img class='soymodulo_logoSoy' src='{$soymanagerpublications_iconoSoy}'>
    </div>
    <div id="soymodulo_textos">
        <p>{l s="This module allows you to create posts and associate them with products." mod='soymanagerpublications'}</p>
    </div>
</div>

{*
   textos de errores
*}

{if isset($soymanagererror)}
    {$soymanagererror}
{/if}
{*
   textos correctos
*}
{if isset($correcto)}
    {$correcto}
{/if}
<div class="alert alert-success soy-publicacion-activa soymanager-oculto" >
    <p>{l s="The publication has been activated" mod='soymanagerpublications'}</p>
</div>

<div class="alert alert-success soy-publicacion-desactivada soymanager-oculto">
    <p>{l s="The publication has been deactivated" mod='soymanagerpublications'}</p>
</div>
