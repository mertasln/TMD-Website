import { component } from 'picoapp';

export default component((node) => {
  const img = node.getElementsByTagName('img')[0];
  node.addEventListener('mouseover', function(){
    node.getElementsByTagName('img')[1].style.display = 'block';
    img.style.zIndex = 1;
  })
  node.addEventListener('mouseout', function(){
    img.style.zIndex = 200; 
  })
});