let dragging = null;
function insertAfter(newNode, existingNode) {
  // 获取现有节点的父元素
  const parent = existingNode.parentNode;

  // 如果父元素中的最后一个子元素 等于 现有的节点
  if (parent.lastChild === existingNode) {
    // 把现有节点放入父元素子节点后面
    // appendChild在子节点后面追加一个元素
    parent.appendChild(newNode);
  } else {
    // .nextSibling 该属性返回指定节点后的第一个节点
    // insertBefore 第一个参数插入的节点对象，第二参数可选，在其之前插入子节点，如果不传，则在结尾插入。
    parent.insertBefore(newNode, existingNode.nextSibling);
  }
}
function drag(event) {
  dragging = event.target;
  event.dataTransfer.setData("index",dragging.attributes.id.value);
}
function onDragOver(event) {
  event.preventDefault();
}
function onDrop(event) {
  event.preventDefault();
  let data=event.target.parentNode;
  let dragIndex = data.attributes.index.value;
  let tagIndex = dragging.attributes.index.value;
  console.log(dragIndex, tagIndex);
  if (dragIndex < tagIndex) data.parentNode.insertBefore(dragging, data);
  else insertAfter(dragging, data);
  let i = 0;
  for (let item of document.getElementsByClassName('el-upload-list--picture-card')[0].getElementsByTagName('li')) {
    item.setAttribute('index', i);
    i++;
  }

}
