<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 	
	xmlns:php="http://php.net/xsl" 
	extension-element-prefixes="php"
>
<xsl:output	method="xml" encoding="UTF-8" indent="yes" />
<xsl:template match="*" >
    <xsl:copy>
        <xsl:for-each select="@*">
	        <xsl:choose>
	        <xsl:when test="name() = '_moz_dirty'">
	        </xsl:when>
	        <xsl:otherwise>
	            <xsl:if test="name() != 'id' or (not(starts-with(.,'ext-')) and not(starts-with(.,'AUIT_')))">
	            	<xsl:copy/>
	            </xsl:if>
	        </xsl:otherwise>
	        </xsl:choose>
        </xsl:for-each>
        <xsl:apply-templates/>
    </xsl:copy>
</xsl:template>

<xsl:template match="comment()">
<xsl:comment>
   <xsl:value-of select="."/>
</xsl:comment>
</xsl:template>
<!-- 
<xsl:template match="area|base|basefont|br|col|frame|hr|img|input|isindex|link|meta|param" >
    <xsl:copy>
        <xsl:for-each select="@*">
	        <xsl:choose>
	        <xsl:when test="name() = '_moz_dirty'">
	        </xsl:when>
	        <xsl:otherwise>
	            <xsl:if test="name() != 'id' or (not(starts-with(.,'ext-')) and not(starts-with(.,'AUIT_')))">
	            	<xsl:copy/>
	            </xsl:if>
	        </xsl:otherwise>
	        </xsl:choose>
        </xsl:for-each>
    </xsl:copy>
</xsl:template>
<xsl:template match="AREA|BASE|BASEFONT|BR|COL|FRAME|HR|IMG|INPUT|ISINDEX|LINK|META|PARAM" >
    <xsl:copy>
        <xsl:for-each select="@*">
            <xsl:if test="name() != 'id' or (not(starts-with(.,'ext-')) and not(starts-with(.,'AUIT_')))">
            	<xsl:copy/>
            </xsl:if>
        </xsl:for-each>
    </xsl:copy>
</xsl:template>
-->

<xsl:template match="SPAN[contains(@class,'auit-edit-block')]" >
<xsl:choose>
<xsl:when test="SPAN[contains(@class,'auit-edit-inlineblock-description')]">
<xsl:text>
{{</xsl:text>
	<xsl:value-of select="SPAN[contains(@class,'auit-edit-inlineblock-description')]"/>
<xsl:text>}}
</xsl:text>
</xsl:when>
<xsl:otherwise>
	<xsl:apply-templates />
</xsl:otherwise>
</xsl:choose>
</xsl:template>


<xsl:template match="DIV[@contenteditable]|div[@contenteditable]" >
	<xsl:apply-templates />
</xsl:template>
<xsl:template match="/" >
	<xsl:apply-templates />
</xsl:template>
</xsl:stylesheet>