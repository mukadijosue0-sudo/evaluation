<style>
    /* ========================================================================== */
    /* Footer */
    /* ========================================================================== */

    .site-footer {
        background: linear-gradient(135deg, var(--menu-bg-start), var(--menu-bg-end));
        color: #D1D5DB;
        padding: 5px 0 0;
        border-top: 1px solid #374151;
        font-family: Arial, Helvetica, sans-serif;
        width: 100%;
    }


    /* -------------------------------------------------------------------------- */
    /* Barre inférieure */
    /* -------------------------------------------------------------------------- */

    .site-footer__bottom {
        width: 100%;
        margin: 25px auto 0;
        padding: 12px 10px;

        border-top: 1px solid rgba(255, 255, 255, 0.5);

        text-align: center;

        color: #FFFFFF;

        font-size: 14px;
        line-height: 1.4;
        box-sizing: border-box;
    }


    /* -------------------------------------------------------------------------- */
    /* Responsive */
    /* -------------------------------------------------------------------------- */

    @media (max-width: 768px) {

        .site-footer {
            padding: 20px 15px 0;
        }

        .site-footer__bottom {
            margin-top: 25px;
        }
    }
</style>


<footer class="site-footer">
    <!-- Copyright -->
    <div class="site-footer__bottom">
        &copy; <?= date('Y') ?> Guy Verghote. Tous droits réservés.
    </div>

</footer>
