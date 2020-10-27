export const getChild = (child, userType) => {
  if (child.parentUserId) {
    switch (userType) {
      case "agent":
        if (!child.agent) {
          return false;
        }
        break;
      case "investor":
        if (!child.investor) {
          return false;
        }
        break;
      case "tenant":
        if (!child.tenant) {
          return false;
        }
        break;
    }
  }
  let avatar = child.avatar ? child.avatar : defaultAvatar;
  let formatedValue = `
      ${child.name} 
      <div class='peer mR-10'>
          <img class='w-4r bdrs-50p' src='${avatar}' alt=''>
      </div>
      `;
  treeData.push([
    {
      v: child.id,
      f: formatedValue,
    },
    `${child.parentUserId}`,
    "",
  ]);

  if (Array.isArray(child.children)) {
    child.children.forEach(function(item) {
      getChild(item, userType);
    });
  }
};

export const drawChart = () => {
  var data = new google.visualization.DataTable();
  data.addColumn("string", "Name");
  data.addColumn("string", "Manager");
  data.addColumn("string", "ToolTip");

  data.addRows(treeData);

  var chart = new google.visualization.OrgChart(
    document.getElementById("chart_div")
  );

  chart.draw(data, { allowHtml: true });
};
