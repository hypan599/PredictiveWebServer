function openCity(evt, cityName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
}

function parseNewick(a) {
	console.log(a)
    for (var e = [], r = {}, s = a.split(/\s*(;|\(|\)|,|:)\s*/), t = 0; t < s.length; t++) {
        var n = s[t];
        switch (n) {
            case"(":
                var c = {};
                r.branchset = [c], e.push(r), r = c;
                break;
            case",":
                var c = {};
                e[e.length - 1].branchset.push(c), r = c;
                break;
            case")":
                r = e.pop();
                break;
            case":":
                break;
            default:
                var h = s[t - 1];
                ")" == h || "(" == h || "," == h ? r.name = n : ":" == h && (r.length = parseFloat(n))
        }
    }
    return r
}

lifee=[]
function ready(life){
	    outerRadius = 960 / 3,
		innerRadius = outerRadius - 170;

	Isolates = [];
	Types = [];
	Time = [];
	SourceState = [];
	SourceSite = [];
	lifee = life
	d3.csv('BIOL7210-Team1-Metadata.csv', function (csv) {
		for (i = 0; i < csv.length; i++) {
			Isolates[i] = csv[i]['Isolate']
			Types[i] = csv[i]['Source Type']
			Time[i] = csv[i]['Isolate Date']
			SourceState[i] = csv[i]['Source State']
			SourceSite[i] = csv[i]['Source Site']
		}

		update(SourceSite,life)
		update_barchart(SourceSite, "source_site.csv")
	})
}

function update_barchart(ColorBy, filename) {
    // create the svg
    var svg = d3.select("#main").append("svg")
            .attr("width", 600)
            .attr("height", 350),
        margin = {top: 20, right: 20, bottom: 30, left: 40},
        width = +svg.attr("width") - margin.left - margin.right,
        height = +svg.attr("height") - margin.top - margin.bottom,
        g = svg.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    // set x scale
    var x = d3.scaleBand()
        .rangeRound([0, width])
        .paddingInner(0.05)
        .align(0.1);

    // set y scale
    var y = d3.scaleLinear()
        .rangeRound([height, 0]);

    // set the colors
    var z = d3.scaleOrdinal()
        .domain(d3.map(ColorBy, function (d) {
            return d;
        }).keys())
        .range(d3.schemeCategory10);

    // load the csv and create the chart
    d3.csv(filename, function (d, i, columns) {
        for (i = 1, t = 0; i < columns.length; ++i) t += d[columns[i]] = +d[columns[i]];
        d.total = t;
        return d;
    }, function (error, data) {
        if (error) throw error;

        var keys = data.columns.slice(1);

        //data.sort(function(a, b) { return b.total - a.total; });
        x.domain(data.map(function (d) {
            return d.term;
        }));
        y.domain([0, d3.max(data, function (d) {
            return d.total;
        })]).nice();
        z.domain(keys);

        g.append("g")
            .selectAll("g")
            .data(d3.stack().keys(keys)(data))
            .enter().append("g")
            .attr("fill", function (d) {
                return z(d.key);
            })
            .selectAll("rect")
            .data(function (d) {
                return d;
            })
            .enter().append("rect")
            .attr("x", function (d) {
                return x(d.data.term);
            })
            .attr("y", function (d) {
                return y(d[1]);
            })
            .attr("height", function (d) {
                return y(d[0]) - y(d[1]);
            })
            .attr("width", x.bandwidth())
            .on("mouseover", function () {
                tooltip.style("display", "block");
            })
            .on("mouseout", function () {
                tooltip.style("display", "none");
            })
            .on("mousemove", function (d) {
                console.log(d);
                var xPosition = d3.mouse(this)[0] - 55;
                var yPosition = d3.mouse(this)[1] - 5;
                tooltip.attr("transform", "translate(" + xPosition + "," + yPosition + ")");
                tooltip.select("text").text("Isolate Count = "+(d[1] - d[0]).toString());
                console.log(tooltip)
            });

        g.append("g")
            .attr("class", "axis")
            .attr("transform", "translate(0," + height + ")")
            .call(d3.axisBottom(x));

        g.append("g")
            .attr("class", "axis")
            .call(d3.axisLeft(y).ticks(null, "s"))
            .append("text")
            .attr("x", 2)
            .attr("y", y(y.ticks().pop()) + 0.5)
            .attr("dy", "0.32em")
            .attr("fill", "#000")
            .attr("font-weight", "bold")
            .attr("text-anchor", "start");
			
			g.append("text")
    .attr("class", "y axis title")
    .text("Isolate Count")
    .attr("x", (-(height/2)))
    .attr("y", -45)
    .attr("dy", "1em")
    .attr("transform", "rotate(-90)")
    .style("text-anchor", "middle");
			

        /*   var legend = g.append("g")
              .attr("font-family", "sans-serif")
              .attr("font-size", 10)
              .attr("text-anchor", "end")
            .selectAll("g")
            .data(keys.slice().reverse())
            .enter().append("g")
              .attr("transform", function(d, i) { return "translate(0," + i * 20 + ")"; });

          legend.append("rect")
              .attr("x", width - 19)
              .attr("width", 19)
              .attr("height", 19)
              .attr("fill", z);

          legend.append("text")
              .attr("x", width - 24)
              .attr("y", 9.5)
              .attr("dy", "0.32em")
              .text(function(d) { return d; }); */
    });

    // Prep the tooltip bits, initial display is hidden
    var tooltip = svg.append("g")
        .attr("class", "tooltip")
		.attr("data-container","body")
        .style("display", "none")
		.style("opacity", 1)
		.style("z-index",5000);

    tooltip.append("rect")
        .attr("width", 120)
        .attr("height", 20)
        .attr("fill", "white")
		.attr("stroke", "black");

    tooltip.append("text")
        .attr("x", 63)
        .attr("dy", "1.2em")
        .style("text-anchor", "middle")
        .attr("font-size", "12px")
        .attr("font-weight", "bold");
}


$("input[name=gender]").click(function () {
    d3.select('#drop').html("")
    d3.select('#main').html("")
    if (this.value == 0) {
        update(SourceSite,lifee)
        update_barchart(SourceSite, "source_site.csv")
    } else if (this.value == 1) {
        update(Types,lifee)
        update_barchart(Types, "source_type.csv")
    } else if (this.value == 2) {
        update(SourceState,lifee)
        update_barchart(SourceState, "state.csv")
    }
});

function update(ColorBy,life) {
    var color = d3.scaleOrdinal()
        .domain(d3.map(ColorBy, function (d) {
            return d;
        }).keys())
        .range(d3.schemeCategory10);

    var cluster = d3.cluster()
        .size([360, innerRadius])
        .separation(function (a, b) {
            return 1;
        });

    var svg = d3.select("#drop").append("svg")
        .attr("width", outerRadius * 2)
        .attr("height", outerRadius * 2);

    var legend = svg.append("g")
        .attr("class", "legend")
        .selectAll("g")
        .data(color.domain())
        .enter().append("g")
        .attr("transform", function (d, i) {
            return "translate(" + (outerRadius * 2 - 10) + "," + (i * 20 + 10) + ")";
        });

    legend.append("rect")
        .attr("x", -18)
        .attr("width", 18)
        .attr("height", 18)
        .attr("fill", color);

    legend.append("text")
        .attr("x", -24)
        .attr("y", 9)
        .attr("dy", ".35em")
        .attr("text-anchor", "end")
        .text(function (d) {
            return d;
        });

    var chart = svg.append("g")
        .attr("transform", "translate(" + outerRadius + "," + outerRadius + ")");


    var root = d3.hierarchy(parseNewick(life), function (d) {
            return d.branchset;
        })
            .sum(function (d) {
                return d.branchset ? 0 : 1;
            })
            .sort(function (a, b) {
                return (a.value - b.value) || d3.ascending(a.data.length, b.data.length);
            });

        cluster(root);

        var input = d3.select("#show-length input").on("change", changed);


        setRadius(root, root.data.length = 0, innerRadius / maxLength(root));
        setColor(root);

        var linkExtension = chart.append("g")
            .attr("class", "link-extensions")
            .selectAll("path")
            .data(root.links().filter(function (d) {
                return !d.target.children;
            }))
            .enter().append("path")
            .each(function (d) {
                d.target.linkExtensionNode = this;
            })
            .attr("d", linkExtensionConstant);

        var link = chart.append("g")
            .attr("class", "links")
            .selectAll("path")
            .data(root.links())
            .enter().append("path")
            .each(function (d) {
                d.target.linkNode = this;
            })
            .attr("d", linkConstant)
            .attr("stroke", function (d) {
                return d.target.color;
            });

        chart.append("g")
            .attr("class", "labels")
            .selectAll("text")
            .data(root.leaves())
            .enter().append("text")
            .attr("dy", ".31em")
            .attr("transform", function (d) {
                return "rotate(" + (d.x - 90) + ")translate(" + (innerRadius + 4) + ",0)" + (d.x < 180 ? "" : "rotate(180)");
            })
            .attr("text-anchor", function (d) {
                return d.x < 180 ? "start" : "end";
            })
            .text(function (d) {
				if(d.data.name == "input")
					return "Input"
                return d.data.name.replace(/_/g, " ");
            })
			.attr('style', function (d) {
				if(d.data.name == "input")
					return "fill:red;font-size:20px !important"
                return "";
            })
            .on("mouseover", mouseovered(true))
            .on("mouseout", mouseovered(false));

        function changed() {
            //clearTimeout(timeout);
            var t = d3.transition().duration(750);
            linkExtension.transition(t).attr("d", this.checked ? linkExtensionVariable : linkExtensionConstant);
            link.transition(t).attr("d", this.checked ? linkVariable : linkConstant);
        }

        function mouseovered(active) {
            return function (d) {
                d3.select(this).classed("label--active", active);
                d3.select(d.linkExtensionNode).classed("link-extension--active", active).each(moveToFront);
                do d3.select(d.linkNode).classed("link--active", active).each(moveToFront); while (d = d.parent);
            };
        }

        function moveToFront() {
            this.parentNode.appendChild(this);
        }

    // Compute the maximum cumulative length of any node in the tree.
    function maxLength(d) {
        return d.data.length + (d.children ? d3.max(d.children, maxLength) : 0);
    }

// Set the radius of each node by recursively summing and scaling the distance from the root.
    function setRadius(d, y0, k) {
        d.radius = (y0 += d.data.length) * k;
        if (d.children) d.children.forEach(function (d) {
            setRadius(d, y0, k);
        });
    }

// Set the color of each node by recursively inheriting.
    function setColor(d) {
        var name = d.data.name;
        name = ColorBy[Isolates.indexOf(name)]
        d.color = color.domain().indexOf(name) >= 0 ? color(name) : d.parent ? d.parent.color : null;
        if (d.children) d.children.forEach(setColor);
    }

    function linkVariable(d) {
        return linkStep(d.source.x, d.source.radius, d.target.x, d.target.radius);
    }

    function linkConstant(d) {
        return linkStep(d.source.x, d.source.y, d.target.x, d.target.y);
    }

    function linkExtensionVariable(d) {
        return linkStep(d.target.x, d.target.radius, d.target.x, innerRadius);
    }

    function linkExtensionConstant(d) {
        return linkStep(d.target.x, d.target.y, d.target.x, innerRadius);
    }

// Like d3.svg.diagonal.radial, but with square corners.
    function linkStep(startAngle, startRadius, endAngle, endRadius) {
        var c0 = Math.cos(startAngle = (startAngle - 90) / 180 * Math.PI),
            s0 = Math.sin(startAngle),
            c1 = Math.cos(endAngle = (endAngle - 90) / 180 * Math.PI),
            s1 = Math.sin(endAngle);
        return "M" + startRadius * c0 + "," + startRadius * s0
            + (endAngle === startAngle ? "" : "A" + startRadius + "," + startRadius + " 0 0 " + (endAngle > startAngle ? 1 : 0) + " " + startRadius * c1 + "," + startRadius * s1)
            + "L" + endRadius * c1 + "," + endRadius * s1;
    }
}