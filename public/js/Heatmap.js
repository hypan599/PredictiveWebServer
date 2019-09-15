function main(vf,amr){
  plot_map("AMR_list.csv", "#my_dataviz", "Antimicrobial Resistance Profile","Antimicrobial resistance genes identified from the CARD database for each isolate",amr)
  plot_map("VFs.csv", "#Virulence", "Virulence Factors","Virulence factors identified from the Virulence Factor Database for each isolate.",vf)
}
dataa=""
function plot_map(filename, div, title, detail, data) {
	dataa=data
	console.log("PLOT_MAP")
    var margin = {top: 80, right: 25, bottom: 30, left: 40},
        width = 450 - margin.left - margin.right,
        height = 470 - margin.top - margin.bottom;

    // append the svg object to the body of the page
    var svg = d3.select(div)
        .append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", 500)
        .append("g")
        .attr("transform",
            "translate(" + margin.left + "," + margin.top + ")");
    //Read the data
    function Heatmap1(data) {
		console.log(data)
        // Labels of row and columns -> unique identifier of the column called 'group' and 'variable'
        myGroups = d3.map(data, function (d) {
            return d.group;
        }).keys()
        myVars = d3.map(data, function (d) {
			if(d.variable == "sample")
				return "Input"
            return d.variable;
        }).keys()
         console.log(myGroups)
		 console.log(myVars)
        // Build X scales and axis:
        var x = d3.scaleBand()
            .range([0, width])
            .domain(myGroups)
            .padding(0.05);
        svg.append("g")
            .style("font-size", 15)
            .attr("transform", "translate(0," + height + ")")
            .call(d3.axisBottom(x).tickSize(0))
            .select(".domain").remove()

        // Build Y scales and axis:
        var y = d3.scaleBand()
            .range([height, 0])
            .domain(myVars)
            .padding(0.05);

        svg.append("g")
            .call(d3.axisLeft(y).tickSize(0))
			.data(myVars)
            .select(".domain").remove()

        // Build color scale
        var myColor = d3.scaleOrdinal()
            .domain([0, 1])
            .range(["white", "#e68a00"]);

        // create a tooltip
        var tooltip = d3.select("#my_dataviz")
            .append("div")
            .style("opacity", 0)
            .attr("class", "tooltip")
            .style("background-color", "white")
            .style("border", "solid")
            .style("border-width", "2px")
            .style("border-radius", "5px")
            .style("padding", "5px")

        // Three function that change the tooltip when user hover / move / leave a cell
        var mouseover = function (d) {
            tooltip
                .style("opacity", 1)
            d3.select(this)
                .style("stroke", "black")
        }
        var mousemove = function (d) {
			var vval = 100
		    if(div == "#Virulence"){vval=800}
			if(d.value == 1){
            tooltip
                .html("Isolate"+d.variable+" is resistant to " + d.group)
                .style("left", (d3.mouse(this)[0] + vval) + "px")
                .style("top", (d3.mouse(this)[1]) +150+ "px")}
			else{
            tooltip
                .html("Isolate"+d.variable+" is not resistant to " + d.group)
                .style("left", (d3.mouse(this)[0] + vval) + "px")
                .style("top", (d3.mouse(this)[1]) +150+ "px")}				
        }
        var mouseleave = function (d) {
            tooltip
                .style("opacity", 0)
            d3.select(this)
                .style("stroke", "none")
                .style("opacity", 0.8)
        }
        // add the squares
        svg.selectAll()
            .data(data, function (d) {
                return d.group + ':' + d.variable;
            })
            .enter()
            .append("rect")
            .attr("x", function (d) {
                return x(d.group)
            })
            .attr("y", function (d) {
                return y(d.variable)
            })
            .attr("rx", 4)
            .attr("ry", 4)
            .attr("width", x.bandwidth())
            .attr("height", y.bandwidth())
            .style("fill", function (d) {
				if(d.variable == "sample" && d.value==1)
					return "red"
                return myColor(d.value)
            })
            .style("stroke-width", 4)
            .style("stroke", "none")
            .style("opacity", 0.8)
            .on("mouseover", mouseover)
            .on("mousemove", mousemove)
            .on("mouseleave", mouseleave)
    }
	
	Heatmap1(data)

    // Add title to graph
    svg.append("text")
        .attr("x", 0)
        .attr("y", -50)
        .attr("text-anchor", "left")
        .style("font-size", "22px")
        .text(title);

    // Add subtitle to graph
    svg.append("text")
        .attr("x", 0)
        .attr("y", -20)
        .attr("text-anchor", "left")
        .style("font-size", "14px")
        .style("fill", "grey")
        .style("max-width", 400)
        .text(detail);
		
		var cellSize = 17;
	    var myColor = ["white", "#e68a00"];	

		
	if(div == "#Virulence"){
		var key = svg.append("g")
            .attr("id","key")
            .attr("class","key")
            .attr("transform", "translate(170,400)");
        
        key.selectAll("rect")
            .data(myColor)
            .enter()
            .append("rect")
            .attr("width",cellSize)
            .attr("height",cellSize)
			.attr("stroke","black")
            .attr("x",function(d,i){
                return i*130;
            })
            .attr("fill",function(d){
                return d;
            });
        
        key.selectAll("text")
            .data(myColor)
            .enter()
            .append("text")
            .attr("x",function(d,i){
                return cellSize+5+(i*130);
            })
            .attr("y","1em")
            .text(function(d,i){
                if (i<myColor.length-1){
                    return "Nonresistant";
                }   else    {
                    return "Resistant";   
                }
	});
	}

}