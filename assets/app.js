// import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.scss';
import 'bootstrap';
//___________
import { createApp } from 'vue';
import App from './vue/app.vue';
//___________

// On crée la vue et on la met sur l'élément qui a le même id
const VueContainer = document.querySelector('#vue-app');
if (VueContainer) {
    const app = createApp(App);
    app.mount('#vue-app');
}

document.querySelectorAll('.btn-like').forEach(button => {
    button.addEventListener('click', (event) => {
        event.stopPropagation();
        const count = button.querySelector('.like-count');
        count.textContent = parseInt(count.textContent) + 1;
    });
});

// Le humberger menu de la navbar en format mobile
const  burger_menu = document.querySelector('.burger_menu');
const navmenu = document.querySelector(".navmenu");
const navmenu_admin = document.querySelector(".navmenu_admin");

if (burger_menu) {
    
    burger_menu.addEventListener('click', () => {
        burger_menu.classList.toggle('activated');
        if(navmenu){
        navmenu.classList.toggle('showed');
        }
        if(navmenu_admin){
        navmenu_admin.classList.toggle('showed');
        }
    });
    }

// C'est la flèche du toggle menu de l'admin en format mobile de la partie admin
const admin_arrow = document.querySelector('.admin_arrow');
const btn_responsive_admin_nav = document.querySelector('.btn_responsive_admin_nav');
const admin_side_bar = document.querySelector('.admin_side_bar');

if (btn_responsive_admin_nav) {
    btn_responsive_admin_nav.addEventListener('click', () => {
        admin_arrow.classList.toggle('fa-flip-horizontal');
        admin_side_bar.classList.toggle('showed');
        btn_responsive_admin_nav.classList.toggle('showed');
    });
}

// La réactivité  de l'input password quand on crée un compte ou qu'on change de mdp

const char_mdp = document.querySelector('.char_mdp');
const majuscule_mdp = document.querySelector('.majuscule_mdp');
const minuscule_mdp = document.querySelector('.minuscule_mdp');
const chiffre_mdp = document.querySelector('.chiffre_mdp');
const special_char_mdp = document.querySelector('.special_char_mdp');
const match_mdp = document.querySelector('.match_mdp');

const all_restrictions = [char_mdp, majuscule_mdp, minuscule_mdp, chiffre_mdp, special_char_mdp, match_mdp];
const password_1 = document.querySelector('.password_1');
const password_2 = document.querySelector('.password_2');
const verification_event = document.querySelector('.verification_event');
let warning = null;

if(password_1){
    password_1.addEventListener('input', () => {

        if(password_1.value.length >= 12){
            all_restrictions[0].classList.add('success_restriction');
        }
        else{
            all_restrictions[0].classList.remove('success_restriction');
        }

        if (password_1.value.match(/[A-Z]/)){
            all_restrictions[1].classList.add('success_restriction');
        }
        else{
            all_restrictions[1].classList.remove('success_restriction');
        }

        if (password_1.value.match(/[a-z]/)){
            all_restrictions[2].classList.add('success_restriction');
        }
        else{
            all_restrictions[2].classList.remove('success_restriction');
        }

        if (password_1.value.match(/[0-9]/)){
            all_restrictions[3].classList.add('success_restriction');
        }
        else{
            all_restrictions[3].classList.remove('success_restriction');
        }

        if (password_1.value.match(/[^A-Za-z0-9]/)){
            all_restrictions[4].classList.add('success_restriction');
        }
        else{
            all_restrictions[4].classList.remove('success_restriction');
        }
        
        if (password_1.value === password_2.value){
            all_restrictions[5].classList.add('success_restriction');
        }
        else{
            all_restrictions[5].classList.remove('success_restriction');
        }
    })
    password_2.addEventListener('input', () => {
        if (password_1.value === password_2.value){
            all_restrictions[5].classList.add('success_restriction');
        }
        else{
            all_restrictions[5].classList.remove('success_restriction');
        }
    })
}
if(verification_event){

    verification_event.addEventListener('click', (event) => {
        let i=0;
        let counter=0;
        while (i != 6) {
            const anim = i;
            if (all_restrictions[i].classList.contains('success_restriction')) {
                all_restrictions[i].classList.remove('warning_restriction');
                counter++;
            } else {
                all_restrictions[i].classList.add('warning_restriction');
                all_restrictions[i].classList.add('shake');
                all_restrictions[i].addEventListener('animationend', () => {
                    all_restrictions[anim].classList.remove('shake');
                })
            }  
            i++;
        }

        if (counter != 6){
            event.preventDefault();
            if(!warning){
                warning = document.createElement('p');
                warning.classList.add('text-danger');
                warning.classList.add('text-center');
                warning.classList.add('fw-bold');
                warning.classList.add('my-auto');
                warning.textContent = 'Le mot de passe doit remplir les conditions ci-dessus';
                document.querySelector('.password_restriction_box').insertAdjacentElement('afterend', warning);
            }
        }
    })
}