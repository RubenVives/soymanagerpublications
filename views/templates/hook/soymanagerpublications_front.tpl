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
{if isset($soymanagerpublications_publicaciones)}
    <div id="soymanagerpublicationes_vistoen">
        <span>{l s='Seen in...' mod='soymanagerpublications'}</span>
    </div><div class="soy-contenedor-publicaciones">

    {foreach from=$soymanagerpublications_publicaciones key=key item=publicacion name=publicaciones}
        <div class="soy-contenedor-publicacion">
            <a href="{$soymanagerpublications_publicaciones[$key]['url']}">
                <img src="{$soymanagerpublications_publicaciones[$key]['image_url']}">
            </a>
        </div>
    {/foreach}
</div>
{/if}