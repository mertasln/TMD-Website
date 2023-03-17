import { component } from 'picoapp';

export default component(({ node }) => {
  document.getElementById("gg-2022-menu").onchange = function() {  
    window.location.href = document.getElementById('gg-2022-menu').value = this.value;;
  }
});