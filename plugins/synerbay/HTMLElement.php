<?php


namespace SynerBay;


class HTMLElement extends WPActionLoader
{
    /** @var OfferApply $offerApply */
    private $offerApply;

    public function __construct()
    {
        include_once __DIR__ . '/backend/modules/OfferApply.php';

        $this->offerApply = new \OfferApply();

        $this->addAction('loader');
        $this->addAction('loginModal');
        $this->addAction('offerApplyButton');
    }

    public function loader()
    {
        echo '
            <div id="example-4" class="loader" style="display: none;">
                <div id="ball-container-1" class="ball-container">
                    <div id="ball-1" class="ball"></div>
                </div>
                <div id="ball-container-2" class="ball-container">
                    <div id="ball-2" class="ball"></div>
                </div>
                <div id="ball-container-3" class="ball-container">
                    <div id="ball-3" class="ball"></div>
                </div>
            </div> 
        ';
    }

    public function loginModal()
    {
        $title = '[angol szöveg!!] popup címe';
        $content = '[angol szöveg!!] A funkciót csak belépés után érhető el!<br>Ugrás a belépés oldalra: <a href="synerbay.com">[Remco kellene a login url - Link]</a>';
        $this->generateModal('login', $title, $content);
    }

    public function offerApplyButton(\WC_Product $product)
    {
        // TODO Remco a wp-ben a product azaz offer?
        // amennyiben a sajátja az offer, akkor nem tud jelentkezni
        if (current_user_can('edit_post', $product->get_id())) {
            echo '';
        }

        // jelentekezett máár rá vagy nem?
        // todo Remco színezd meg légyszi a gombokat és a js függvényx bemenő paraméterének kellene az offer id
        if (!get_current_user_id() || (get_current_user_id() && !$this->offerApply->isUserAppliedOffer(get_current_user_id(), $product->get_id()))) {
            echo  "<button type='button' style='background-color: green !important;' onclick='window.synerbay.appearOffer(".$product->get_id().")' class='button'>[angol szöveg] jelentkezés</div></button>";
        } else {
            echo  "<button type='button' onclick='window.synerbay.disAppearOffer(".$product->get_id().")' class='button'>[angol szöveg] lejelentkezés</div></button>";
        }
    }

    private function generateModal($name, $title, $content)
    {
        echo '
            <div id="mf-'. $name .'-popup" class="martfury-modal mf-'.$name.'-popup mf-newsletter-popup" tabindex="-1" aria-hidden="true">
                <div class="mf-modal-overlay"></div>
                <div class="modal-content">
                <div class="modal-header" style="padding-left: 20px;">
                    <h5 class="modal-title">'.$title.'</h5>
                </div>
                <a href="#" class="close-modal">
                    <i class="icon-cross"></i>
                </a>
                <div class="newletter-content" style="margin-bottom: 30px;">
                    '. $content .'
                </div>
                </div>
            </div>
        ';
    }
}